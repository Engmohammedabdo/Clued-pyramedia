<?php
/**
 * PYRAMEDIA - Testimonials API
 * RESTful API for testimonials management
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

class TestimonialsAPI {
    private Database $db;
    private string $lang;

    public function __construct() {
        $this->db = getDB();
        $this->lang = $_GET['lang'] ?? 'en';
    }

    public function handleRequest(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? 'list';

        try {
            $response = match($action) {
                'list' => $this->getTestimonials(),
                'single' => $this->getSingleTestimonial(),
                'featured' => $this->getFeaturedTestimonials(),
                'submit' => $this->submitTestimonial(),
                'stats' => $this->getStats(),
                default => $this->error('Invalid action', 400)
            };

            $this->success($response);
        } catch (Exception $e) {
            error_log("Testimonials API error: " . $e->getMessage());
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get paginated testimonials list
     */
    private function getTestimonials(): array {
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 12);
        $status = $_GET['status'] ?? 'approved';
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT
            t.id,
            t.client_name,
            t.client_position,
            t.client_company,
            t.client_avatar,
            t.rating,
            " . ($this->lang === 'ar' && !empty('t.testimonial_text_ar') ? 'COALESCE(t.testimonial_text_ar, t.testimonial_text)' : 't.testimonial_text') . " as testimonial_text,
            t.project_type,
            t.featured,
            t.created_at,
            ta.views,
            ta.likes,
            ta.shares
        FROM testimonials t
        LEFT JOIN testimonials_analytics ta ON t.id = ta.testimonial_id
        WHERE t.status = :status
        ORDER BY t.display_order ASC, t.created_at DESC
        LIMIT :limit OFFSET :offset";

        $params = [
            'status' => $status,
            'limit' => $perPage,
            'offset' => $offset
        ];

        $testimonials = $this->db->all($sql, $params);

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM testimonials WHERE status = :status";
        $totalResult = $this->db->single($countSql, ['status' => $status]);
        $total = (int)$totalResult['total'];

        return [
            'testimonials' => $testimonials,
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
     * Get single testimonial
     */
    private function getSingleTestimonial(): array {
        $id = (int)($_GET['id'] ?? 0);

        if (!$id) {
            throw new Exception('Testimonial ID is required');
        }

        $sql = "SELECT
            t.*,
            ta.views,
            ta.likes,
            ta.shares
        FROM testimonials t
        LEFT JOIN testimonials_analytics ta ON t.id = ta.testimonial_id
        WHERE t.id = :id";

        $testimonial = $this->db->single($sql, ['id' => $id]);

        if (!$testimonial) {
            throw new Exception('Testimonial not found', 404);
        }

        // Increment views
        $this->db->execute(
            "UPDATE testimonials_analytics SET views = views + 1 WHERE testimonial_id = :id",
            ['id' => $id]
        );

        return $testimonial;
    }

    /**
     * Get featured testimonials for carousel
     */
    private function getFeaturedTestimonials(): array {
        $limit = (int)($_GET['limit'] ?? 5);

        $sql = "SELECT
            t.id,
            t.client_name,
            t.client_position,
            t.client_company,
            t.client_avatar,
            t.rating,
            " . ($this->lang === 'ar' ? 'COALESCE(t.testimonial_text_ar, t.testimonial_text)' : 't.testimonial_text') . " as testimonial_text,
            t.project_type,
            ta.views
        FROM testimonials t
        LEFT JOIN testimonials_analytics ta ON t.id = ta.testimonial_id
        WHERE t.status = 'approved' AND t.featured = TRUE
        ORDER BY t.display_order ASC
        LIMIT :limit";

        return $this->db->all($sql, ['limit' => $limit]);
    }

    /**
     * Submit new testimonial
     */
    private function submitTestimonial(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Method not allowed', 405);
        }

        // Get JSON data
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required = ['client_name', 'client_email', 'testimonial_text'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '{$field}' is required");
            }
        }

        // Validate email
        if (!filter_var($data['client_email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }

        // Prepare data
        $testimonialData = [
            'client_name' => $this->sanitize($data['client_name']),
            'client_position' => $this->sanitize($data['client_position'] ?? ''),
            'client_company' => $this->sanitize($data['client_company'] ?? ''),
            'client_email' => $this->sanitize($data['client_email']),
            'client_avatar' => $this->generateAvatar($data['client_name']),
            'rating' => min(5, max(1, (int)($data['rating'] ?? 5))),
            'testimonial_text' => $this->sanitize($data['testimonial_text']),
            'project_type' => $this->sanitize($data['project_type'] ?? ''),
            'status' => 'pending', // All new testimonials need approval
            'created_at' => date('Y-m-d H:i:s')
        ];

        $testimonialId = $this->db->insert('testimonials', $testimonialData);

        // Create analytics entry
        $this->db->insert('testimonials_analytics', [
            'testimonial_id' => $testimonialId,
            'views' => 0,
            'likes' => 0,
            'shares' => 0
        ]);

        return [
            'message' => 'Thank you for your testimonial! It will be reviewed and published soon.',
            'testimonial_id' => $testimonialId
        ];
    }

    /**
     * Get statistics
     */
    private function getStats(): array {
        $stats = [];

        // Total testimonials
        $result = $this->db->single("SELECT COUNT(*) as total FROM testimonials WHERE status = 'approved'");
        $stats['total_testimonials'] = (int)$result['total'];

        // Average rating
        $result = $this->db->single("SELECT AVG(rating) as avg_rating FROM testimonials WHERE status = 'approved'");
        $stats['average_rating'] = round((float)$result['avg_rating'], 1);

        // Total views
        $result = $this->db->single("SELECT SUM(views) as total_views FROM testimonials_analytics");
        $stats['total_views'] = (int)$result['total_views'];

        // Pending approvals
        $result = $this->db->single("SELECT COUNT(*) as pending FROM testimonials WHERE status = 'pending'");
        $stats['pending_approvals'] = (int)$result['pending'];

        return $stats;
    }

    /**
     * Generate avatar URL
     */
    private function generateAvatar(string $name): string {
        $name = urlencode($name);
        $colors = ['FF6B35', '4A90E2', '10B981', 'EC4899', '8B5CF6', 'F59E0B'];
        $color = $colors[array_rand($colors)];
        return "https://ui-avatars.com/api/?name={$name}&background={$color}&color=fff&size=200&bold=true";
    }

    /**
     * Sanitize input
     */
    private function sanitize(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Success response
     */
    private function success(mixed $data): void {
        echo json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Error response
     */
    private function error(string $message, int $code = 400): never {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Handle request
$api = new TestimonialsAPI();
$api->handleRequest();
