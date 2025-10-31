<?php
/**
 * PYRAMEDIA Admin - Posts Management
 * Comprehensive CRUD system for blog posts
 */

declare(strict_types=1);

define('ADMIN_ACCESS', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

$auth = getAuth();
$auth->requireLogin();

$db = getDB();
$user = $auth->getCurrentUser();

$action = $_GET['action'] ?? 'list';
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';

    if (!$auth->verifyCsrfToken($csrf)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        switch ($action) {
            case 'create':
            case 'edit':
                $result = savePost($db, $user, $action === 'edit' ? $postId : 0);
                if ($result['success']) {
                    $success = $result['message'];
                    if ($action === 'create') {
                        $postId = $result['post_id'];
                        $action = 'edit';
                    }
                } else {
                    $error = $result['error'];
                }
                break;

            case 'delete':
                if ($auth->isAdmin() || isPostAuthor($db, $postId, $user['id'])) {
                    deletePost($db, $postId);
                    header('Location: posts.php?deleted=1');
                    exit;
                } else {
                    $error = 'You do not have permission to delete this post.';
                }
                break;

            case 'bulk':
                $result = handleBulkAction($db, $user);
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['error'];
                }
                $action = 'list';
                break;
        }
    }
}

// Get post data for edit
$post = null;
if ($action === 'edit' && $postId > 0) {
    $post = getPost($db, $postId);
    if (!$post) {
        $error = 'Post not found.';
        $action = 'list';
    } elseif (!$auth->isAdmin() && $post['author_id'] != $user['id']) {
        $error = 'You do not have permission to edit this post.';
        $action = 'list';
    }
}

// Get categories and tags for form
$categories = getCategories($db);
$tags = getTags($db);

$pageTitle = match($action) {
    'create' => 'Create New Post',
    'edit' => 'Edit Post',
    default => 'Posts'
};

include __DIR__ . '/includes/header.php';
?>

<!-- Main Content -->
<div class="flex-1 overflow-x-hidden overflow-y-auto">
    <?php if ($action === 'list'): ?>
        <?php include __DIR__ . '/pages/posts-list.php'; ?>
    <?php else: ?>
        <?php include __DIR__ . '/pages/posts-form.php'; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php

/**
 * Save post (create or update)
 */
function savePost(Database $db, array $user, int $postId): array {
    try {
        // Validate required fields
        $required = ['title_en', 'content_en', 'category_id'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                return ['success' => false, 'error' => "Field '{$field}' is required"];
            }
        }

        // Prepare data
        $data = [
            'title_en' => trim($_POST['title_en']),
            'title_ar' => trim($_POST['title_ar'] ?? $_POST['title_en']),
            'slug_en' => generateSlug($_POST['slug_en'] ?? $_POST['title_en']),
            'slug_ar' => generateSlug($_POST['slug_ar'] ?? $_POST['title_ar'] ?? $_POST['title_en']),
            'content_en' => $_POST['content_en'],
            'content_ar' => $_POST['content_ar'] ?? $_POST['content_en'],
            'excerpt_en' => trim($_POST['excerpt_en'] ?? ''),
            'excerpt_ar' => trim($_POST['excerpt_ar'] ?? ''),
            'category_id' => (int)$_POST['category_id'],
            'featured_image' => trim($_POST['featured_image'] ?? ''),
            'featured_image_alt' => trim($_POST['featured_image_alt'] ?? ''),
            'meta_description_en' => trim($_POST['meta_description_en'] ?? ''),
            'meta_description_ar' => trim($_POST['meta_description_ar'] ?? ''),
            'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
            'status' => $_POST['status'] ?? 'draft',
            'read_time' => !empty($_POST['read_time']) ? (int)$_POST['read_time'] : calculateReadTime($_POST['content_en']),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Set published_at if publishing
        if ($data['status'] === 'published' && $postId === 0) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($postId > 0) {
            // Update existing post
            $db->update('blog_posts', $data, 'id = :id', ['id' => $postId]);
            $message = 'Post updated successfully!';
        } else {
            // Create new post
            $data['author_id'] = $user['id'];
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['views'] = 0;

            if ($data['status'] === 'published') {
                $data['published_at'] = date('Y-m-d H:i:s');
            }

            $postId = $db->insert('blog_posts', $data);
            $message = 'Post created successfully!';
        }

        // Update tags
        if (isset($_POST['tags']) && is_array($_POST['tags'])) {
            updatePostTags($db, $postId, $_POST['tags']);
        }

        return [
            'success' => true,
            'message' => $message,
            'post_id' => $postId
        ];

    } catch (Exception $e) {
        error_log("Save post error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'An error occurred while saving the post.'
        ];
    }
}

/**
 * Get single post
 */
function getPost(Database $db, int $postId): ?array {
    $stmt = $db->getConnection()->prepare("
        SELECT p.*, u.full_name as author_name
        FROM blog_posts p
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.id = :id
    ");
    $stmt->execute(['id' => $postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Get tags
        $stmt = $db->getConnection()->prepare("
            SELECT t.id, t.name_en, t.name_ar
            FROM blog_tags t
            INNER JOIN blog_post_tags pt ON t.id = pt.tag_id
            WHERE pt.post_id = :post_id
        ");
        $stmt->execute(['post_id' => $postId]);
        $post['tags'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $post ?: null;
}

/**
 * Delete post
 */
function deletePost(Database $db, int $postId): void {
    // Delete tags relationship
    $db->delete('blog_post_tags', 'post_id = :post_id', ['post_id' => $postId]);

    // Delete post
    $db->delete('blog_posts', 'id = :id', ['id' => $postId]);
}

/**
 * Check if user is post author
 */
function isPostAuthor(Database $db, int $postId, int $userId): bool {
    $stmt = $db->getConnection()->prepare("
        SELECT COUNT(*) as count FROM blog_posts WHERE id = :id AND author_id = :user_id
    ");
    $stmt->execute(['id' => $postId, 'user_id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
}

/**
 * Update post tags
 */
function updatePostTags(Database $db, int $postId, array $tagIds): void {
    // Delete existing tags
    $db->delete('blog_post_tags', 'post_id = :post_id', ['post_id' => $postId]);

    // Insert new tags
    foreach ($tagIds as $tagId) {
        $db->insert('blog_post_tags', [
            'post_id' => $postId,
            'tag_id' => (int)$tagId
        ]);
    }
}

/**
 * Get all categories
 */
function getCategories(Database $db): array {
    $stmt = $db->getConnection()->query("
        SELECT id, name_en, name_ar FROM blog_categories ORDER BY name_en
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all tags
 */
function getTags(Database $db): array {
    $stmt = $db->getConnection()->query("
        SELECT id, name_en, name_ar FROM blog_tags ORDER BY name_en
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Generate URL-friendly slug
 */
function generateSlug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Calculate reading time
 */
function calculateReadTime(string $content): int {
    $wordCount = str_word_count(strip_tags($content));
    return max(1, (int)ceil($wordCount / 200));
}

/**
 * Handle bulk actions
 */
function handleBulkAction(Database $db, array $user): array {
    $bulkAction = $_POST['bulk_action'] ?? '';
    $postIds = $_POST['post_ids'] ?? [];

    if (empty($bulkAction) || empty($postIds)) {
        return ['success' => false, 'error' => 'No action or posts selected'];
    }

    $count = 0;

    foreach ($postIds as $id) {
        $postId = (int)$id;

        switch ($bulkAction) {
            case 'delete':
                if ($user['role'] === 'admin' || isPostAuthor($db, $postId, $user['id'])) {
                    deletePost($db, $postId);
                    $count++;
                }
                break;

            case 'publish':
                $db->update('blog_posts', [
                    'status' => 'published',
                    'published_at' => date('Y-m-d H:i:s')
                ], 'id = :id', ['id' => $postId]);
                $count++;
                break;

            case 'draft':
                $db->update('blog_posts', [
                    'status' => 'draft'
                ], 'id = :id', ['id' => $postId]);
                $count++;
                break;
        }
    }

    return [
        'success' => true,
        'message' => "{$count} post(s) {$bulkAction}ed successfully"
    ];
}
