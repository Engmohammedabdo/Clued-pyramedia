# 🔗 n8n Webhook Integration Guide for PYRAMEDIA Blog

## 📋 Overview

This guide explains how to integrate n8n automation platform with the PYRAMEDIA blog system to enable automatic blog post publishing via webhooks.

---

## 🎯 Use Cases

1. **Automatic Publishing**: Publish blog posts from external sources (CMS, social media, etc.)
2. **Content Distribution**: Automatically cross-post content to multiple platforms
3. **Scheduled Publishing**: Schedule posts via n8n workflows
4. **Content Updates**: Update existing posts automatically
5. **Bulk Import**: Import posts from RSS feeds, APIs, or databases

---

## 🔧 Setup Requirements

### Prerequisites
- n8n instance (cloud or self-hosted)
- PYRAMEDIA website with blog API installed
- Database access credentials
- Basic understanding of webhooks and HTTP requests

### Database Tables
The blog system uses these tables:
- `blog_posts` - Main posts table
- `blog_categories` - Categories
- `blog_tags` - Tags
- `blog_post_tags` - Many-to-many relationship
- `users` - Authors

---

## 📡 Webhook Endpoint

### Create Webhook Endpoint

**File**: `php/blog-webhook.php` (Create this file)

```php
<?php
/**
 * PYRAMEDIA - n8n Webhook Handler
 * Receives webhook calls from n8n for automatic blog publishing
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

// Configuration
define('WEBHOOK_SECRET', 'your-secure-webhook-secret-key-here'); // CHANGE THIS!

/**
 * Webhook Handler Class
 */
class BlogWebhookHandler {
    private Database $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Verify webhook signature
     */
    private function verifySignature(string $payload, string $signature): bool {
        $expectedSignature = hash_hmac('sha256', $payload, WEBHOOK_SECRET);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Create new blog post
     */
    public function createPost(array $data): array {
        try {
            // Validate required fields
            $required = ['title_en', 'content_en', 'author_id', 'category_id'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: {$field}");
                }
            }

            // Generate slug if not provided
            if (empty($data['slug_en'])) {
                $data['slug_en'] = $this->generateSlug($data['title_en']);
            }
            if (empty($data['slug_ar']) && !empty($data['title_ar'])) {
                $data['slug_ar'] = $this->generateSlug($data['title_ar']);
            }

            // Set defaults
            $postData = [
                'title_en' => $data['title_en'],
                'title_ar' => $data['title_ar'] ?? $data['title_en'],
                'slug_en' => $data['slug_en'],
                'slug_ar' => $data['slug_ar'] ?? $data['slug_en'],
                'content_en' => $data['content_en'],
                'content_ar' => $data['content_ar'] ?? $data['content_en'],
                'excerpt_en' => $data['excerpt_en'] ?? $this->generateExcerpt($data['content_en']),
                'excerpt_ar' => $data['excerpt_ar'] ?? $this->generateExcerpt($data['content_ar'] ?? $data['content_en']),
                'author_id' => (int)$data['author_id'],
                'category_id' => (int)$data['category_id'],
                'featured_image' => $data['featured_image'] ?? null,
                'featured_image_alt' => $data['featured_image_alt'] ?? '',
                'meta_description_en' => $data['meta_description_en'] ?? $this->generateExcerpt($data['content_en'], 160),
                'meta_description_ar' => $data['meta_description_ar'] ?? $this->generateExcerpt($data['content_ar'] ?? $data['content_en'], 160),
                'meta_keywords' => $data['meta_keywords'] ?? '',
                'status' => $data['status'] ?? 'published',
                'read_time' => $data['read_time'] ?? $this->calculateReadTime($data['content_en']),
                'published_at' => $data['published_at'] ?? date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Insert post
            $postId = $this->db->insert('blog_posts', $postData);

            if (!$postId) {
                throw new Exception('Failed to insert post');
            }

            // Add tags if provided
            if (!empty($data['tags']) && is_array($data['tags'])) {
                $this->addTags($postId, $data['tags']);
            }

            return [
                'success' => true,
                'post_id' => $postId,
                'message' => 'Post created successfully',
                'url_en' => "https://pyramedia.ae/blog-post.html?slug={$postData['slug_en']}",
                'url_ar' => "https://pyramedia.ae/blog-post-ar.html?slug={$postData['slug_ar']}"
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update existing post
     */
    public function updatePost(int $postId, array $data): array {
        try {
            $updateData = [];

            // Only update provided fields
            $allowedFields = [
                'title_en', 'title_ar', 'slug_en', 'slug_ar',
                'content_en', 'content_ar', 'excerpt_en', 'excerpt_ar',
                'category_id', 'featured_image', 'featured_image_alt',
                'meta_description_en', 'meta_description_ar', 'meta_keywords',
                'status', 'read_time', 'published_at'
            ];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            $updateData['updated_at'] = date('Y-m-d H:i:s');

            $result = $this->db->update(
                'blog_posts',
                $updateData,
                'id = :id',
                ['id' => $postId]
            );

            // Update tags if provided
            if (isset($data['tags']) && is_array($data['tags'])) {
                // Remove old tags
                $this->db->delete('blog_post_tags', 'post_id = :post_id', ['post_id' => $postId]);
                // Add new tags
                $this->addTags($postId, $data['tags']);
            }

            return [
                'success' => true,
                'post_id' => $postId,
                'message' => 'Post updated successfully'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete post
     */
    public function deletePost(int $postId): array {
        try {
            // Delete tags relationship
            $this->db->delete('blog_post_tags', 'post_id = :post_id', ['post_id' => $postId]);

            // Delete post
            $result = $this->db->delete('blog_posts', 'id = :id', ['id' => $postId]);

            return [
                'success' => true,
                'message' => 'Post deleted successfully'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Add tags to post
     */
    private function addTags(int $postId, array $tags): void {
        foreach ($tags as $tagId) {
            $this->db->insert('blog_post_tags', [
                'post_id' => $postId,
                'tag_id' => (int)$tagId
            ]);
        }
    }

    /**
     * Generate URL-friendly slug
     */
    private function generateSlug(string $text): string {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $text = trim($text, '-');

        // Add timestamp to ensure uniqueness
        return $text . '-' . time();
    }

    /**
     * Generate excerpt from content
     */
    private function generateExcerpt(string $content, int $length = 200): string {
        $text = strip_tags($content);
        if (strlen($text) <= $length) {
            return $text;
        }

        $excerpt = substr($text, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');

        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }

        return $excerpt . '...';
    }

    /**
     * Calculate reading time
     */
    private function calculateReadTime(string $content): int {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int)ceil($wordCount / 200)); // 200 words per minute
    }
}

// Main handler
try {
    // Get request body
    $payload = file_get_contents('php://input');
    $data = json_decode($payload, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload');
    }

    // Verify signature (recommended for production)
    $signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';
    $handler = new BlogWebhookHandler();

    // Uncomment in production:
    // if (!$handler->verifySignature($payload, $signature)) {
    //     throw new Exception('Invalid signature');
    // }

    // Handle action
    $action = $data['action'] ?? 'create';

    $response = match($action) {
        'create' => $handler->createPost($data),
        'update' => $handler->updatePost((int)$data['post_id'], $data),
        'delete' => $handler->deletePost((int)$data['post_id']),
        default => ['success' => false, 'error' => 'Invalid action']
    };

    http_response_code($response['success'] ? 200 : 400);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
```

---

## 🚀 n8n Workflow Setup

### 1. Basic Workflow Structure

```
Trigger (Webhook/Schedule/RSS)
    → Data Processing
    → HTTP Request (POST to webhook)
    → Success/Error Handling
```

### 2. Create Webhook Node in n8n

1. **Add Webhook Node** (Trigger)
   - Method: POST
   - Path: `/pyramedia-blog`
   - Authentication: Header Auth (optional)

2. **Add HTTP Request Node**
   - URL: `https://pyramedia.ae/php/blog-webhook.php`
   - Method: POST
   - Headers:
     - `Content-Type`: `application/json`
     - `X-Webhook-Signature`: `{{ $node["Function"].json["signature"] }}`

### 3. Example Payload Structure

#### Create Post
```json
{
  "action": "create",
  "title_en": "The Future of Digital Marketing",
  "title_ar": "مستقبل التسويق الرقمي",
  "content_en": "<p>Full article content in HTML...</p>",
  "content_ar": "<p>محتوى المقال الكامل بالعربية...</p>",
  "excerpt_en": "Short description of the article",
  "excerpt_ar": "وصف قصير للمقال",
  "author_id": 1,
  "category_id": 2,
  "tags": [1, 3, 5],
  "featured_image": "https://example.com/image.jpg",
  "featured_image_alt": "Marketing illustration",
  "meta_description_en": "Learn about the future of digital marketing",
  "meta_description_ar": "تعرف على مستقبل التسويق الرقمي",
  "meta_keywords": "marketing, digital, future, trends",
  "status": "published",
  "published_at": "2025-01-15 10:00:00"
}
```

#### Update Post
```json
{
  "action": "update",
  "post_id": 123,
  "title_en": "Updated Title",
  "content_en": "<p>Updated content...</p>",
  "status": "published"
}
```

#### Delete Post
```json
{
  "action": "delete",
  "post_id": 123
}
```

---

## 📊 Sample n8n Workflows

### Workflow 1: RSS Feed Auto-Publish

```
RSS Feed Reader
    → Filter (new items only)
    → Data Transformation
    → HTTP Request (Create Post)
    → Slack Notification (success)
```

### Workflow 2: Scheduled Content from Google Sheets

```
Schedule Trigger (daily at 9 AM)
    → Google Sheets (Read rows)
    → Loop Items
    → HTTP Request (Create Post)
    → Update Sheet (mark as published)
```

### Workflow 3: Content from External API

```
HTTP Request (External API)
    → Data Processing
    → Image Download & Upload
    → HTTP Request (Create Post with image)
    → Send Email Notification
```

---

## 🔒 Security Best Practices

### 1. Webhook Secret

Generate a secure webhook secret:
```bash
php -r "echo bin2hex(random_bytes(32));"
```

Update in `php/blog-webhook.php`:
```php
define('WEBHOOK_SECRET', 'your-generated-secret-here');
```

### 2. Signature Verification

In n8n, add a Function node before HTTP Request:
```javascript
// Generate HMAC signature
const crypto = require('crypto');
const payload = JSON.stringify($input.all()[0].json);
const signature = crypto
  .createHmac('sha256', 'your-webhook-secret')
  .update(payload)
  .digest('hex');

return {
  json: {
    signature: signature,
    payload: $input.all()[0].json
  }
};
```

### 3. IP Whitelisting

Add to `.htaccess` or nginx config:
```apache
# .htaccess
<Files "blog-webhook.php">
    Require ip 123.456.789.0  # Your n8n server IP
    Require ip 10.0.0.0/8     # Private network if needed
</Files>
```

### 4. Rate Limiting

Add to webhook handler:
```php
// Simple rate limiting
$cacheFile = '/tmp/webhook_rate_limit.txt';
$lastRequest = @file_get_contents($cacheFile);

if ($lastRequest && (time() - (int)$lastRequest) < 5) {
    http_response_code(429);
    die('Rate limit exceeded');
}

file_put_contents($cacheFile, time());
```

---

## 🧪 Testing

### Using cURL
```bash
curl -X POST https://pyramedia.ae/php/blog-webhook.php \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Signature: your-signature-here" \
  -d '{
    "action": "create",
    "title_en": "Test Post",
    "content_en": "<p>Test content</p>",
    "author_id": 1,
    "category_id": 1
  }'
```

### Using Postman
1. Method: POST
2. URL: `https://pyramedia.ae/php/blog-webhook.php`
3. Headers:
   - `Content-Type`: `application/json`
4. Body (raw JSON): Use example payload above

---

## 📝 Database Reference

### Get Author IDs
```sql
SELECT id, full_name, email FROM users WHERE role = 'author';
```

### Get Category IDs
```sql
SELECT id, name_en, name_ar, slug FROM blog_categories;
```

### Get Tag IDs
```sql
SELECT id, name_en, name_ar, slug FROM blog_tags;
```

---

## 🐛 Troubleshooting

### Common Issues

1. **"Missing required field" error**
   - Ensure all required fields are present: `title_en`, `content_en`, `author_id`, `category_id`

2. **"Failed to insert post" error**
   - Check database credentials in `config/database.php`
   - Verify author_id and category_id exist

3. **Invalid signature error**
   - Verify webhook secret matches in both n8n and PHP
   - Check signature generation code

4. **404 Not Found**
   - Verify webhook file exists at `php/blog-webhook.php`
   - Check file permissions (644)

### Enable Debug Mode

Add to webhook handler:
```php
// Log requests for debugging
file_put_contents(
    '/tmp/webhook_debug.log',
    date('Y-m-d H:i:s') . " - " . $payload . "\n",
    FILE_APPEND
);
```

---

## 📚 Additional Resources

- [n8n Documentation](https://docs.n8n.io/)
- [n8n Webhook Node](https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-base.webhook/)
- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [HMAC Authentication](https://en.wikipedia.org/wiki/HMAC)

---

## 📞 Support

For questions or issues:
- Email: info@pyramedia.ae
- Check logs: `/tmp/webhook_debug.log`
- Database errors: Check MySQL error logs

---

**Built with ❤️ for PYRAMEDIA Blog System**
