<?php
/**
 * PYRAMEDIA - Auto-save Draft Endpoint
 * Saves post content every 30 seconds to prevent data loss
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

$auth = getAuth();

// Check authentication
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$db = getDB();
$user = $auth->getCurrentUser();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

try {
    $postId = (int)($input['post_id'] ?? 0);
    $action = $input['action'] ?? 'autosave';

    // Validate required fields for autosave
    if (empty($input['title_en']) && empty($input['content_en'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Nothing to save',
            'timestamp' => time()
        ]);
        exit;
    }

    // Prepare autosave data
    $data = [
        'title_en' => trim($input['title_en'] ?? ''),
        'title_ar' => trim($input['title_ar'] ?? ''),
        'content_en' => $input['content_en'] ?? '',
        'content_ar' => $input['content_ar'] ?? '',
        'excerpt_en' => trim($input['excerpt_en'] ?? ''),
        'excerpt_ar' => trim($input['excerpt_ar'] ?? ''),
        'category_id' => !empty($input['category_id']) ? (int)$input['category_id'] : null,
        'featured_image' => trim($input['featured_image'] ?? ''),
        'featured_image_alt' => trim($input['featured_image_alt'] ?? ''),
        'meta_description_en' => trim($input['meta_description_en'] ?? ''),
        'meta_description_ar' => trim($input['meta_description_ar'] ?? ''),
        'meta_keywords' => trim($input['meta_keywords'] ?? ''),
        'status' => 'draft', // Auto-saves always save as draft
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Generate slugs if we have titles
    if (!empty($data['title_en'])) {
        $data['slug_en'] = generateSlug($input['slug_en'] ?? $data['title_en']);
        $data['slug_ar'] = generateSlug($input['slug_ar'] ?? $data['title_ar'] ?? $data['title_en']);
    }

    // Calculate reading time if we have content
    if (!empty($data['content_en'])) {
        $data['read_time'] = calculateReadTime($data['content_en']);
    }

    if ($postId > 0) {
        // Update existing draft
        // First check if user has permission
        $stmt = $db->getConnection()->prepare("SELECT author_id FROM blog_posts WHERE id = :id");
        $stmt->execute(['id' => $postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            echo json_encode(['success' => false, 'error' => 'Post not found']);
            exit;
        }

        if ($post['author_id'] != $user['id'] && !$auth->isAdmin()) {
            echo json_encode(['success' => false, 'error' => 'Permission denied']);
            exit;
        }

        // Update the post
        $db->update('blog_posts', $data, 'id = :id', ['id' => $postId]);

        echo json_encode([
            'success' => true,
            'action' => 'updated',
            'post_id' => $postId,
            'timestamp' => time(),
            'message' => 'Draft auto-saved'
        ]);

    } else {
        // Create new draft
        $data['author_id'] = $user['id'];
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['views'] = 0;

        // Make sure slugs are unique
        if (isset($data['slug_en'])) {
            $data['slug_en'] = ensureUniqueSlug($db, $data['slug_en'], 0);
        }
        if (isset($data['slug_ar'])) {
            $data['slug_ar'] = ensureUniqueSlug($db, $data['slug_ar'], 0, 'slug_ar');
        }

        $newPostId = $db->insert('blog_posts', $data);

        echo json_encode([
            'success' => true,
            'action' => 'created',
            'post_id' => $newPostId,
            'timestamp' => time(),
            'message' => 'Draft created'
        ]);
    }

} catch (Exception $e) {
    error_log("Auto-save error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error during auto-save',
        'timestamp' => time()
    ]);
}

/**
 * Ensure slug is unique by appending number if needed
 */
function ensureUniqueSlug(Database $db, string $slug, int $excludeId, string $field = 'slug_en'): string {
    $originalSlug = $slug;
    $counter = 1;

    while (true) {
        $stmt = $db->getConnection()->prepare("
            SELECT id FROM blog_posts WHERE $field = :slug AND id != :exclude_id
        ");
        $stmt->execute(['slug' => $slug, 'exclude_id' => $excludeId]);

        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = $originalSlug . '-' . $counter;
        $counter++;

        // Safety limit
        if ($counter > 100) {
            return $originalSlug . '-' . time();
        }
    }
}
