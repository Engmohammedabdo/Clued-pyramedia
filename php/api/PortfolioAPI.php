<?php
/**
 * Portfolio API - RESTful API for portfolio management
 *
 * Endpoints:
 * - GET    /api/portfolio?action=list          - List all published projects
 * - GET    /api/portfolio?action=get&id=X      - Get single project by ID
 * - GET    /api/portfolio?action=featured      - Get featured projects
 * - GET    /api/portfolio?action=categories    - Get all categories
 * - POST   /api/portfolio?action=create        - Create new project (admin only)
 * - PUT    /api/portfolio?action=update&id=X   - Update project (admin only)
 * - DELETE /api/portfolio?action=delete&id=X   - Delete project (admin only)
 *
 * @package PYRAMEDIA\API
 * @version 1.0.0
 * @since 2025-10-31
 * @author PYRAMEDIA Development Team
 * @license MIT
 *
 * @example
 * ```javascript
 * // Fetch all projects
 * fetch('/api/portfolio?action=list&lang=en&category=ecommerce')
 *   .then(res => res.json())
 *   .then(data => console.log(data.projects));
 * ```
 */

require_once __DIR__ . '/BaseAPI.php';

class PortfolioAPI extends BaseAPI {
    /**
     * Rate limit for portfolio API
     * @var int
     */
    protected $rateLimit = 200; // 200 requests per hour

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Main request handler
     *
     * Routes requests to appropriate methods based on action parameter
     *
     * @return void
     */
    public function handleRequest(): void {
        $action = $this->getParam('action', 'list');

        // Route to appropriate handler
        switch ($action) {
            case 'list':
                $this->listProjects();
                break;

            case 'get':
                $this->getProject();
                break;

            case 'featured':
                $this->getFeaturedProjects();
                break;

            case 'categories':
                $this->getCategories();
                break;

            case 'tags':
                $this->getTags();
                break;

            case 'search':
                $this->searchProjects();
                break;

            case 'increment_view':
                $this->incrementViewCount();
                break;

            // Admin actions (require authentication)
            case 'create':
                $this->requireAuth();
                $this->createProject();
                break;

            case 'update':
                $this->requireAuth();
                $this->updateProject();
                break;

            case 'delete':
                $this->requireAuth();
                $this->deleteProject();
                break;

            default:
                $this->error('Invalid action', 400);
        }
    }

    /**
     * List all published projects with filtering and pagination
     *
     * Query Parameters:
     * - lang: Language code (en/ar)
     * - category: Category slug to filter by
     * - featured: Only featured projects (1/0)
     * - limit: Number of results (default: 20, max: 100)
     * - offset: Pagination offset (default: 0)
     * - sort: Sort field (default: published_at)
     * - order: Sort order (ASC/DESC, default: DESC)
     *
     * @return void
     */
    private function listProjects(): void {
        // Get parameters
        $lang = $this->getParam('lang', 'en');
        $category = $this->getParam('category');
        $featured = $this->getParam('featured');
        $limit = min((int)$this->getParam('limit', 20), 100);
        $offset = max((int)$this->getParam('offset', 0), 0);
        $sort = $this->getParam('sort', 'published_at');
        $order = strtoupper($this->getParam('order', 'DESC'));

        // Validate language
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }

        // Validate sort order
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'DESC';
        }

        // Validate sort field
        $allowed_sorts = ['published_at', 'view_count', 'title_en', 'title_ar', 'display_order'];
        if (!in_array($sort, $allowed_sorts)) {
            $sort = 'published_at';
        }

        // Build query
        $sql = "
            SELECT
                p.id,
                p.slug,
                p.title_{$lang} as title,
                p.client_name_{$lang} as client_name,
                p.description_{$lang} as description,
                p.featured_image,
                p.view_count,
                p.like_count,
                p.published_at,
                p.is_featured,
                c.slug as category_slug,
                c.name_{$lang} as category_name,
                c.color as category_color
            FROM portfolio_projects p
            INNER JOIN portfolio_categories c ON p.category_id = c.id
            WHERE p.status = 'published'
              AND p.deleted_at IS NULL
              AND c.is_active = TRUE
        ";

        $params = [];

        // Add category filter
        if ($category) {
            $sql .= " AND c.slug = ?";
            $params[] = $category;
        }

        // Add featured filter
        if ($featured !== null) {
            $sql .= " AND p.is_featured = ?";
            $params[] = (int)$featured;
        }

        // Add sorting
        $sql .= " ORDER BY p.{$sort} {$order}";

        // Add pagination
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        try {
            // Execute query
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($params);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get metrics and tags for each project
            foreach ($projects as &$project) {
                $project['metrics'] = $this->getProjectMetrics($project['id'], $lang);
                $project['tags'] = $this->getProjectTags($project['id'], $lang);
            }

            // Get total count
            $count_sql = "
                SELECT COUNT(*) as total
                FROM portfolio_projects p
                INNER JOIN portfolio_categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                  AND p.deleted_at IS NULL
                  AND c.is_active = TRUE
            ";

            $count_params = [];

            if ($category) {
                $count_sql .= " AND c.slug = ?";
                $count_params[] = $category;
            }

            if ($featured !== null) {
                $count_sql .= " AND p.is_featured = ?";
                $count_params[] = (int)$featured;
            }

            $count_stmt = $this->db->conn->prepare($count_sql);
            $count_stmt->execute($count_params);
            $total = (int)$count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Return response
            $this->success([
                'projects' => $projects,
                'pagination' => [
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + $limit) < $total
                ]
            ]);

        } catch (PDOException $e) {
            error_log('Portfolio API Error: ' . $e->getMessage());
            $this->error('Failed to fetch projects', 500);
        }
    }

    /**
     * Get a single project by ID or slug
     *
     * Query Parameters:
     * - id: Project ID (int) or slug (string)
     * - lang: Language code (en/ar)
     *
     * @return void
     */
    private function getProject(): void {
        $id = $this->getParam('id');
        $slug = $this->getParam('slug');
        $lang = $this->getParam('lang', 'en');

        // Validate language
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }

        if (!$id && !$slug) {
            $this->error('Missing required parameter: id or slug', 400);
        }

        $sql = "
            SELECT
                p.id,
                p.slug,
                p.title_{$lang} as title,
                p.client_name_{$lang} as client_name,
                p.description_{$lang} as description,
                p.featured_image,
                p.project_url,
                p.project_year,
                p.project_duration,
                p.view_count,
                p.like_count,
                p.share_count,
                p.published_at,
                p.services,
                p.technologies,
                c.slug as category_slug,
                c.name_{$lang} as category_name,
                c.color as category_color
            FROM portfolio_projects p
            INNER JOIN portfolio_categories c ON p.category_id = c.id
            WHERE p.status = 'published'
              AND p.deleted_at IS NULL
              AND c.is_active = TRUE
        ";

        if ($id && is_numeric($id)) {
            $sql .= " AND p.id = ?";
            $param = (int)$id;
        } else {
            $sql .= " AND p.slug = ?";
            $param = $slug ?? $id;
        }

        try {
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute([$param]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$project) {
                $this->error('Project not found', 404);
            }

            // Decode JSON fields
            $project['services'] = json_decode($project['services'], true);
            $project['technologies'] = json_decode($project['technologies'], true);

            // Get metrics
            $project['metrics'] = $this->getProjectMetrics($project['id'], $lang);

            // Get tags
            $project['tags'] = $this->getProjectTags($project['id'], $lang);

            // Get images
            $project['images'] = $this->getProjectImages($project['id'], $lang);

            $this->success($project);

        } catch (PDOException $e) {
            error_log('Portfolio API Error: ' . $e->getMessage());
            $this->error('Failed to fetch project', 500);
        }
    }

    /**
     * Get featured projects
     *
     * Query Parameters:
     * - lang: Language code (en/ar)
     * - limit: Number of results (default: 5, max: 20)
     *
     * @return void
     */
    private function getFeaturedProjects(): void {
        $lang = $this->getParam('lang', 'en');
        $limit = min((int)$this->getParam('limit', 5), 20);

        // Validate language
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }

        $sql = "
            SELECT
                p.id,
                p.slug,
                p.title_{$lang} as title,
                p.client_name_{$lang} as client_name,
                p.description_{$lang} as description,
                p.featured_image,
                c.slug as category_slug,
                c.name_{$lang} as category_name,
                c.color as category_color
            FROM portfolio_projects p
            INNER JOIN portfolio_categories c ON p.category_id = c.id
            WHERE p.is_featured = TRUE
              AND p.status = 'published'
              AND p.deleted_at IS NULL
              AND c.is_active = TRUE
            ORDER BY p.display_order ASC, p.published_at DESC
            LIMIT ?
        ";

        try {
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute([$limit]);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get metrics for each project
            foreach ($projects as &$project) {
                $project['metrics'] = $this->getProjectMetrics($project['id'], $lang);
                $project['tags'] = $this->getProjectTags($project['id'], $lang);
            }

            $this->success($projects);

        } catch (PDOException $e) {
            error_log('Portfolio API Error: ' . $e->getMessage());
            $this->error('Failed to fetch featured projects', 500);
        }
    }

    /**
     * Get all categories
     *
     * Query Parameters:
     * - lang: Language code (en/ar)
     *
     * @return void
     */
    private function getCategories(): void {
        $lang = $this->getParam('lang', 'en');

        // Validate language
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }

        $sql = "
            SELECT
                slug,
                name_{$lang} as name,
                description_{$lang} as description,
                icon,
                color,
                (SELECT COUNT(*) FROM portfolio_projects WHERE category_id = portfolio_categories.id AND status = 'published' AND deleted_at IS NULL) as project_count
            FROM portfolio_categories
            WHERE is_active = TRUE
            ORDER BY display_order ASC
        ";

        try {
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->success($categories);

        } catch (PDOException $e) {
            error_log('Portfolio API Error: ' . $e->getMessage());
            $this->error('Failed to fetch categories', 500);
        }
    }

    /**
     * Get all tags
     *
     * Query Parameters:
     * - lang: Language code (en/ar)
     * - type: Filter by tag type (service, technology, industry, skill)
     *
     * @return void
     */
    private function getTags(): void {
        $lang = $this->getParam('lang', 'en');
        $type = $this->getParam('type');

        // Validate language
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }

        $sql = "
            SELECT
                slug,
                name_{$lang} as name,
                type,
                icon,
                color,
                usage_count
            FROM portfolio_tags
            WHERE 1=1
        ";

        $params = [];

        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY usage_count DESC, name_{$lang} ASC";

        try {
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($params);
            $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->success($tags);

        } catch (PDOException $e) {
            error_log('Portfolio API Error: ' . $e->getMessage());
            $this->error('Failed to fetch tags', 500);
        }
    }

    /**
     * Search projects by keyword
     *
     * Uses full-text search on title, client name, and description
     *
     * Query Parameters:
     * - q: Search query (required)
     * - lang: Language code (en/ar)
     * - limit: Number of results (default: 20, max: 100)
     *
     * @return void
     */
    private function searchProjects(): void {
        $query = $this->getParam('q');
        $lang = $this->getParam('lang', 'en');
        $limit = min((int)$this->getParam('limit', 20), 100);

        if (!$query) {
            $this->error('Missing required parameter: q', 400);
        }

        // Validate language
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }

        $sql = "
            SELECT
                p.id,
                p.slug,
                p.title_{$lang} as title,
                p.client_name_{$lang} as client_name,
                p.description_{$lang} as description,
                p.featured_image,
                c.slug as category_slug,
                c.name_{$lang} as category_name,
                c.color as category_color,
                MATCH(p.title_{$lang}, p.client_name_{$lang}, p.description_{$lang}) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
            FROM portfolio_projects p
            INNER JOIN portfolio_categories c ON p.category_id = c.id
            WHERE MATCH(p.title_{$lang}, p.client_name_{$lang}, p.description_{$lang}) AGAINST(? IN NATURAL LANGUAGE MODE)
              AND p.status = 'published'
              AND p.deleted_at IS NULL
            ORDER BY relevance DESC
            LIMIT ?
        ";

        try {
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute([$query, $query, $limit]);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get metrics and tags
            foreach ($projects as &$project) {
                $project['metrics'] = $this->getProjectMetrics($project['id'], $lang);
                $project['tags'] = $this->getProjectTags($project['id'], $lang);
            }

            $this->success([
                'query' => $query,
                'results' => $projects,
                'count' => count($projects)
            ]);

        } catch (PDOException $e) {
            error_log('Portfolio API Error: ' . $e->getMessage());
            $this->error('Search failed', 500);
        }
    }

    /**
     * Increment view count for a project
     *
     * Query Parameters:
     * - id: Project ID (required)
     *
     * @return void
     */
    private function incrementViewCount(): void {
        $id = (int)$this->getParam('id');

        if (!$id) {
            $this->error('Missing required parameter: id', 400);
        }

        try {
            $sql = "
                UPDATE portfolio_projects
                SET view_count = view_count + 1
                WHERE id = ? AND status = 'published' AND deleted_at IS NULL
            ";

            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                $this->success(null, 'View count incremented');
            } else {
                $this->error('Project not found', 404);
            }

        } catch (PDOException $e) {
            error_log('Portfolio API Error: ' . $e->getMessage());
            $this->error('Failed to increment view count', 500);
        }
    }

    /**
     * Get project metrics
     *
     * @param int $projectId Project ID
     * @param string $lang Language code
     * @return array Array of metrics
     */
    private function getProjectMetrics(int $projectId, string $lang = 'en'): array {
        $sql = "
            SELECT
                metric_key,
                metric_value,
                metric_label_{$lang} as label,
                metric_type,
                icon,
                color
            FROM portfolio_metrics
            WHERE project_id = ?
            ORDER BY display_order ASC
        ";

        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get project tags
     *
     * @param int $projectId Project ID
     * @param string $lang Language code
     * @return array Array of tags
     */
    private function getProjectTags(int $projectId, string $lang = 'en'): array {
        $sql = "
            SELECT
                t.slug,
                t.name_{$lang} as name,
                t.type,
                t.icon,
                t.color
            FROM portfolio_tags t
            INNER JOIN portfolio_project_tags pt ON t.id = pt.tag_id
            WHERE pt.project_id = ?
            ORDER BY t.type ASC, t.name_{$lang} ASC
        ";

        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get project images
     *
     * @param int $projectId Project ID
     * @param string $lang Language code
     * @return array Array of images
     */
    private function getProjectImages(int $projectId, string $lang = 'en'): array {
        $sql = "
            SELECT
                image_url,
                image_type,
                title_{$lang} as title,
                caption_{$lang} as caption,
                alt_text_{$lang} as alt_text
            FROM portfolio_images
            WHERE project_id = ?
            ORDER BY display_order ASC
        ";

        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if authentication is required and throw error if not authenticated
     *
     * @return void
     */
    private function requireAuth(): void {
        if (!$this->checkAuth()) {
            $this->error('Authentication required', 401);
        }
    }

    /**
     * Create a new project (admin only)
     *
     * @return void
     */
    private function createProject(): void {
        // Validate required fields
        $this->validateRequired([
            'title_en', 'title_ar', 'description_en', 'description_ar',
            'category_id', 'featured_image'
        ]);

        // TODO: Implement project creation
        // This would involve inserting into portfolio_projects table
        // and handling metrics, tags, images, etc.

        $this->success(null, 'Project creation endpoint - To be implemented', 501);
    }

    /**
     * Update an existing project (admin only)
     *
     * @return void
     */
    private function updateProject(): void {
        $this->validateRequired(['id']);

        // TODO: Implement project update
        // This would involve updating the portfolio_projects table
        // and related tables (metrics, tags, images, etc.)

        $this->success(null, 'Project update endpoint - To be implemented', 501);
    }

    /**
     * Delete a project (soft delete, admin only)
     *
     * @return void
     */
    private function deleteProject(): void {
        $id = (int)$this->getParam('id');

        if (!$id) {
            $this->error('Missing required parameter: id', 400);
        }

        try {
            // Soft delete
            $sql = "
                UPDATE portfolio_projects
                SET deleted_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ";

            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                $this->success(null, 'Project deleted successfully');
            } else {
                $this->error('Project not found', 404);
            }

        } catch (PDOException $e) {
            error_log('Portfolio API Error: ' . $e->getMessage());
            $this->error('Failed to delete project', 500);
        }
    }
}

// Initialize and handle request
$api = new PortfolioAPI();
$api->handleRequest();
