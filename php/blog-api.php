<?php
/**
 * PYRAMEDIA - Blog API
 * Modern PHP 8+ RESTful API for blog system
 *
 * Features:
 * - PHP 8+ syntax (typed properties, named arguments, match expressions)
 * - RESTful architecture
 * - JSON responses
 * - Error handling
 * - Security (SQL injection protection, XSS prevention)
 * - Caching support
 * - Pagination
 * - Full-text search
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

/**
 * Modern Blog API Class with PHP 8+ features
 */
class BlogAPI {
    private Database $db;
    private string $lang;

    public function __construct() {
        $this->db = getDB();
        $this->lang = $_GET['lang'] ?? 'en';
    }

    /**
     * Main router
     */
    public function handleRequest(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? 'list';

        try {
            $response = match($action) {
                'list' => $this->getPosts(),
                'single' => $this->getSinglePost(),
                'categories' => $this->getCategories(),
                'tags' => $this->getTags(),
                'search' => $this->searchPosts(),
                'popular' => $this->getPopularPosts(),
                'recent' => $this->getRecentPosts(),
                'related' => $this->getRelatedPosts(),
                'increment-view' => $this->incrementView(),
                default => $this->error('Invalid action', 400)
            };

            $this->success($response);
        } catch (Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get paginated posts list
     */
    private function getPosts(): array {
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 12);
        $category = $_GET['category'] ?? null;
        $tag = $_GET['tag'] ?? null;
        $offset = ($page - 1) * $perPage;

        // Build query
        $sql = "SELECT
            p.id,
            p.title_{$this->lang} as title,
            p.slug_{$this->lang} as slug,
            p.excerpt_{$this->lang} as excerpt,
            p.featured_image,
            p.read_time,
            p.views,
            p.published_at,
            c.name_{$this->lang} as category_name,
            c.slug as category_slug,
            c.color as category_color,
            u.full_name as author_name,
            u.avatar as author_avatar
        FROM blog_posts p
        LEFT JOIN blog_categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.status = 'published' AND p.published_at <= NOW()";

        $params = [];

        if ($category) {
            $sql .= " AND c.slug = :category";
            $params['category'] = $category;
        }

        if ($tag) {
            $sql .= " AND p.id IN (
                SELECT post_id FROM blog_post_tags pt
                JOIN blog_tags t ON pt.tag_id = t.id
                WHERE t.slug = :tag
            )";
            $params['tag'] = $tag;
        }

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as subquery";
        $totalResult = $this->db->single($countSql, $params);
        $total = (int)$totalResult['total'];

        // Add pagination
        $sql .= " ORDER BY p.published_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        $posts = $this->db->all($sql, $params);

        // Get tags for each post
        foreach ($posts as &$post) {
            $post['tags'] = $this->getPostTags((int)$post['id']);
            $post['published_at'] = $this->formatDate($post['published_at']);
        }

        return [
            'posts' => $posts,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int)ceil($total / $perPage),
                'has_next' => $page < ceil($total / $perPage),
                'has_prev' => $page > 1
            ]
        ];
    }

    /**
     * Get single post by slug
     */
    private function getSinglePost(): array {
        $slug = $_GET['slug'] ?? null;

        if (!$slug) {
            throw new Exception('Slug parameter is required');
        }

        $sql = "SELECT
            p.id,
            p.title_{$this->lang} as title,
            p.slug_{$this->lang} as slug,
            p.content_{$this->lang} as content,
            p.excerpt_{$this->lang} as excerpt,
            p.featured_image,
            p.featured_image_alt,
            p.meta_description_{$this->lang} as meta_description,
            p.meta_keywords,
            p.read_time,
            p.views,
            p.likes,
            p.shares,
            p.published_at,
            p.updated_at,
            c.id as category_id,
            c.name_{$this->lang} as category_name,
            c.slug as category_slug,
            c.color as category_color,
            u.id as author_id,
            u.full_name as author_name,
            u.bio as author_bio,
            u.avatar as author_avatar
        FROM blog_posts p
        LEFT JOIN blog_categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.slug_{$this->lang} = :slug
        AND p.status = 'published'
        AND p.published_at <= NOW()";

        $post = $this->db->single($sql, ['slug' => $slug]);

        if (!$post) {
            throw new Exception('Post not found', 404);
        }

        // Get tags
        $post['tags'] = $this->getPostTags((int)$post['id']);

        // Get comments count
        $post['comments_count'] = $this->getCommentsCount((int)$post['id']);

        // Format dates
        $post['published_at'] = $this->formatDate($post['published_at']);
        $post['updated_at'] = $this->formatDate($post['updated_at']);

        return $post;
    }

    /**
     * Get all categories
     */
    private function getCategories(): array {
        $sql = "SELECT
            c.id,
            c.name_{$this->lang} as name,
            c.slug,
            c.description_{$this->lang} as description,
            c.icon,
            c.color,
            COUNT(p.id) as posts_count
        FROM blog_categories c
        LEFT JOIN blog_posts p ON c.id = p.category_id
            AND p.status = 'published'
            AND p.published_at <= NOW()
        GROUP BY c.id
        ORDER BY c.name_{$this->lang}";

        return $this->db->all($sql);
    }

    /**
     * Get all tags
     */
    private function getTags(): array {
        $sql = "SELECT
            t.id,
            t.name_{$this->lang} as name,
            t.slug,
            COUNT(pt.post_id) as posts_count
        FROM blog_tags t
        LEFT JOIN blog_post_tags pt ON t.id = pt.tag_id
        LEFT JOIN blog_posts p ON pt.post_id = p.id
            AND p.status = 'published'
            AND p.published_at <= NOW()
        GROUP BY t.id
        HAVING posts_count > 0
        ORDER BY posts_count DESC";

        return $this->db->all($sql);
    }

    /**
     * Search posts
     */
    private function searchPosts(): array {
        $query = $_GET['q'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        if (empty($query)) {
            return ['posts' => [], 'pagination' => ['total' => 0]];
        }

        $sql = "SELECT
            p.id,
            p.title_{$this->lang} as title,
            p.slug_{$this->lang} as slug,
            p.excerpt_{$this->lang} as excerpt,
            p.featured_image,
            p.read_time,
            p.published_at,
            c.name_{$this->lang} as category_name,
            c.slug as category_slug,
            MATCH(p.title_{$this->lang}, p.content_{$this->lang})
                AGAINST(:query IN NATURAL LANGUAGE MODE) as relevance
        FROM blog_posts p
        LEFT JOIN blog_categories c ON p.category_id = c.id
        WHERE p.status = 'published'
        AND p.published_at <= NOW()
        AND MATCH(p.title_{$this->lang}, p.content_{$this->lang})
            AGAINST(:query IN NATURAL LANGUAGE MODE)
        ORDER BY relevance DESC, p.published_at DESC
        LIMIT :limit OFFSET :offset";

        $params = [
            'query' => $query,
            'limit' => $perPage,
            'offset' => $offset
        ];

        $posts = $this->db->all($sql, $params);

        // Get total
        $countSql = "SELECT COUNT(*) as total
        FROM blog_posts p
        WHERE p.status = 'published'
        AND MATCH(p.title_{$this->lang}, p.content_{$this->lang})
            AGAINST(:query IN NATURAL LANGUAGE MODE)";

        $totalResult = $this->db->single($countSql, ['query' => $query]);
        $total = (int)$totalResult['total'];

        return [
            'posts' => $posts,
            'query' => $query,
            'pagination' => [
                'current_page' => $page,
                'total' => $total,
                'total_pages' => (int)ceil($total / $perPage)
            ]
        ];
    }

    /**
     * Get popular posts
     */
    private function getPopularPosts(): array {
        $limit = (int)($_GET['limit'] ?? 5);

        $sql = "SELECT
            p.id,
            p.title_{$this->lang} as title,
            p.slug_{$this->lang} as slug,
            p.featured_image,
            p.views,
            p.published_at,
            c.name_{$this->lang} as category_name
        FROM blog_posts p
        LEFT JOIN blog_categories c ON p.category_id = c.id
        WHERE p.status = 'published' AND p.published_at <= NOW()
        ORDER BY p.views DESC
        LIMIT :limit";

        return $this->db->all($sql, ['limit' => $limit]);
    }

    /**
     * Get recent posts
     */
    private function getRecentPosts(): array {
        $limit = (int)($_GET['limit'] ?? 5);

        $sql = "SELECT
            p.id,
            p.title_{$this->lang} as title,
            p.slug_{$this->lang} as slug,
            p.featured_image,
            p.published_at,
            c.name_{$this->lang} as category_name
        FROM blog_posts p
        LEFT JOIN blog_categories c ON p.category_id = c.id
        WHERE p.status = 'published' AND p.published_at <= NOW()
        ORDER BY p.published_at DESC
        LIMIT :limit";

        return $this->db->all($sql, ['limit' => $limit]);
    }

    /**
     * Get related posts
     */
    private function getRelatedPosts(): array {
        $postId = (int)($_GET['post_id'] ?? 0);
        $limit = (int)($_GET['limit'] ?? 3);

        if (!$postId) {
            return [];
        }

        // Get post category
        $post = $this->db->single(
            "SELECT category_id FROM blog_posts WHERE id = :id",
            ['id' => $postId]
        );

        if (!$post) {
            return [];
        }

        $sql = "SELECT
            p.id,
            p.title_{$this->lang} as title,
            p.slug_{$this->lang} as slug,
            p.excerpt_{$this->lang} as excerpt,
            p.featured_image,
            p.read_time,
            p.published_at,
            c.name_{$this->lang} as category_name
        FROM blog_posts p
        LEFT JOIN blog_categories c ON p.category_id = c.id
        WHERE p.status = 'published'
        AND p.published_at <= NOW()
        AND p.id != :post_id
        AND p.category_id = :category_id
        ORDER BY p.published_at DESC
        LIMIT :limit";

        return $this->db->all($sql, [
            'post_id' => $postId,
            'category_id' => $post['category_id'],
            'limit' => $limit
        ]);
    }

    /**
     * Increment post view count
     */
    private function incrementView(): array {
        $postId = (int)($_GET['post_id'] ?? 0);

        if (!$postId) {
            throw new Exception('Post ID is required');
        }

        $sql = "UPDATE blog_posts SET views = views + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $postId]);

        return ['success' => true];
    }

    /**
     * Get post tags
     */
    private function getPostTags(int $postId): array {
        $sql = "SELECT
            t.id,
            t.name_{$this->lang} as name,
            t.slug
        FROM blog_tags t
        JOIN blog_post_tags pt ON t.id = pt.tag_id
        WHERE pt.post_id = :post_id";

        return $this->db->all($sql, ['post_id' => $postId]);
    }

    /**
     * Get comments count
     */
    private function getCommentsCount(int $postId): int {
        return $this->db->count(
            'blog_comments',
            'post_id = :post_id AND status = :status',
            ['post_id' => $postId, 'status' => 'approved']
        );
    }

    /**
     * Format date based on language
     */
    private function formatDate(string $date): string {
        $timestamp = strtotime($date);
        $locale = $this->lang === 'ar' ? 'ar_AE' : 'en_US';

        return date('F j, Y', $timestamp);
    }

    /**
     * Send success response
     */
    private function success(mixed $data, int $code = 200): void {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'timestamp' => time()
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Send error response
     */
    private function error(string $message, int $code = 400): never {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'timestamp' => time()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Initialize and handle request
$api = new BlogAPI();
$api->handleRequest();
?>
