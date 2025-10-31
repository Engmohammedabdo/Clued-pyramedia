<?php
/**
 * BaseAPI Class - Foundation for all API endpoints
 *
 * Provides common functionality for API endpoints including:
 * - Request/Response handling
 * - Authentication & Authorization
 * - Rate limiting
 * - CORS handling
 * - Error responses
 * - Input validation & sanitization
 *
 * @package PYRAMEDIA\API
 * @version 1.0.0
 * @since 2025-10-31
 * @author PYRAMEDIA Development Team
 * @license MIT
 *
 * @example
 * ```php
 * class MyAPI extends BaseAPI {
 *     protected $requiresAuth = false;
 *
 *     public function handleRequest() {
 *         $data = $this->getData();
 *         return $this->success($data);
 *     }
 * }
 * ```
 */

// Require database connection
require_once __DIR__ . '/../../config/database.php';

abstract class BaseAPI {
    /**
     * Database connection instance
     * @var Database
     */
    protected $db;

    /**
     * HTTP request method (GET, POST, PUT, DELETE)
     * @var string
     */
    protected $method;

    /**
     * Request data from $_GET, $_POST, or php://input
     * @var array
     */
    protected $requestData;

    /**
     * Whether authentication is required for this endpoint
     * @var bool
     */
    protected $requiresAuth = false;

    /**
     * Current authenticated user (if logged in)
     * @var array|null
     */
    protected $currentUser = null;

    /**
     * Rate limit: Max requests per time window
     * @var int
     */
    protected $rateLimit = 100;

    /**
     * Rate limit time window in seconds
     * @var int
     */
    protected $rateLimitWindow = 3600; // 1 hour

    /**
     * Allowed HTTP methods for this endpoint
     * @var array
     */
    protected $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];

    /**
     * Constructor - Initialize API
     *
     * @throws Exception If database connection fails
     */
    public function __construct() {
        // Initialize database
        $this->db = getDB();

        // Get request method
        $this->method = $_SERVER['REQUEST_METHOD'];

        // Handle CORS preflight
        if ($this->method === 'OPTIONS') {
            $this->handleCORS();
            exit;
        }

        // Set CORS headers
        $this->handleCORS();

        // Parse request data
        $this->parseRequestData();

        // Check rate limit
        if (!$this->checkRateLimit()) {
            $this->error('Rate limit exceeded. Please try again later.', 429);
        }

        // Check authentication if required
        if ($this->requiresAuth && !$this->checkAuth()) {
            $this->error('Authentication required', 401);
        }

        // Check if method is allowed
        if (!in_array($this->method, $this->allowedMethods)) {
            $this->error('Method not allowed', 405);
        }
    }

    /**
     * Handle CORS (Cross-Origin Resource Sharing) headers
     *
     * Security Note: In production, replace * with specific allowed origins
     *
     * @return void
     */
    protected function handleCORS(): void {
        // Get origin from request
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

        // In production, whitelist specific origins
        $allowedOrigins = [
            'https://ccode.pyramedia.info',
            'https://pyramedia.info',
            'http://localhost:3000' // Development only
        ];

        // Check if origin is allowed (in production mode)
        // For now, allow all origins (development mode)
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 3600');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    /**
     * Parse request data based on HTTP method
     *
     * Handles GET parameters, POST form data, and JSON payloads
     *
     * @return void
     */
    protected function parseRequestData(): void {
        $this->requestData = [];

        switch ($this->method) {
            case 'GET':
                $this->requestData = $_GET;
                break;

            case 'POST':
            case 'PUT':
            case 'DELETE':
                // Check Content-Type
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

                if (strpos($contentType, 'application/json') !== false) {
                    // Parse JSON body
                    $json = file_get_contents('php://input');
                    $this->requestData = json_decode($json, true) ?? [];
                } else {
                    // Parse form data
                    $this->requestData = $_POST;
                }
                break;
        }

        // Sanitize all input data
        $this->requestData = $this->sanitizeArray($this->requestData);
    }

    /**
     * Sanitize an array of data recursively
     *
     * @param array $data Data to sanitize
     * @return array Sanitized data
     */
    protected function sanitizeArray(array $data): array {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $sanitizedKey = $this->sanitize($key);

            if (is_array($value)) {
                $sanitized[$sanitizedKey] = $this->sanitizeArray($value);
            } else {
                $sanitized[$sanitizedKey] = $this->sanitize($value);
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize a single value
     *
     * Removes HTML tags and trims whitespace
     * Does NOT escape for database (use prepared statements instead)
     *
     * @param string $value Value to sanitize
     * @return string Sanitized value
     */
    protected function sanitize($value): string {
        if (!is_string($value)) {
            return $value;
        }

        // Strip HTML tags
        $value = strip_tags($value);

        // Trim whitespace
        $value = trim($value);

        return $value;
    }

    /**
     * Check rate limit for current IP address
     *
     * Uses database-backed rate limiting
     * Creates rate_limits table if it doesn't exist
     *
     * @return bool True if within rate limit, false if exceeded
     */
    protected function checkRateLimit(): bool {
        // Get client IP
        $ip = $this->getClientIP();

        // Create rate_limits table if it doesn't exist
        $this->db->conn->exec("
            CREATE TABLE IF NOT EXISTS rate_limits (
                ip VARCHAR(45) PRIMARY KEY,
                requests INT UNSIGNED DEFAULT 1,
                window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_window (window_start)
            ) ENGINE=InnoDB
        ");

        // Clean up old entries
        $cutoff = date('Y-m-d H:i:s', time() - $this->rateLimitWindow);
        $this->db->conn->exec("DELETE FROM rate_limits WHERE window_start < '$cutoff'");

        // Check current rate
        $stmt = $this->db->conn->prepare("
            SELECT requests, window_start
            FROM rate_limits
            WHERE ip = ?
        ");
        $stmt->execute([$ip]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Check if window has expired
            $windowStart = strtotime($result['window_start']);
            $now = time();

            if (($now - $windowStart) > $this->rateLimitWindow) {
                // Reset window
                $stmt = $this->db->conn->prepare("
                    UPDATE rate_limits
                    SET requests = 1, window_start = CURRENT_TIMESTAMP
                    WHERE ip = ?
                ");
                $stmt->execute([$ip]);
                return true;
            }

            // Check if limit exceeded
            if ($result['requests'] >= $this->rateLimit) {
                return false;
            }

            // Increment counter
            $stmt = $this->db->conn->prepare("
                UPDATE rate_limits
                SET requests = requests + 1
                WHERE ip = ?
            ");
            $stmt->execute([$ip]);
        } else {
            // First request from this IP
            $stmt = $this->db->conn->prepare("
                INSERT INTO rate_limits (ip, requests, window_start)
                VALUES (?, 1, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([$ip]);
        }

        return true;
    }

    /**
     * Get client IP address
     *
     * Checks various headers for proxy/CDN scenarios
     *
     * @return string Client IP address
     */
    protected function getClientIP(): string {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }

    /**
     * Check authentication
     *
     * Validates session-based authentication
     *
     * @return bool True if authenticated, false otherwise
     */
    protected function checkAuth(): bool {
        session_start();

        if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            // Get user data
            $stmt = $this->db->conn->prepare("
                SELECT id, username, email, role
                FROM users
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $this->currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

            return !empty($this->currentUser);
        }

        return false;
    }

    /**
     * Get request parameter with optional default value
     *
     * @param string $key Parameter name
     * @param mixed $default Default value if parameter not found
     * @return mixed Parameter value or default
     */
    protected function getParam(string $key, $default = null) {
        return $this->requestData[$key] ?? $default;
    }

    /**
     * Validate required parameters exist in request
     *
     * @param array $required Array of required parameter names
     * @return bool True if all required parameters exist
     */
    protected function validateRequired(array $required): bool {
        foreach ($required as $param) {
            if (!isset($this->requestData[$param]) || $this->requestData[$param] === '') {
                $this->error("Missing required parameter: $param", 400);
                return false;
            }
        }
        return true;
    }

    /**
     * Send success response
     *
     * @param mixed $data Response data
     * @param string $message Optional success message
     * @param int $code HTTP status code (default: 200)
     * @return void
     */
    protected function success($data = null, string $message = 'Success', int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c') // ISO 8601 format
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Send error response
     *
     * @param string $message Error message
     * @param int $code HTTP status code (default: 400)
     * @param array|null $errors Optional array of detailed errors
     * @return void
     */
    protected function error(string $message, int $code = 400, ?array $errors = null): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => false,
            'error' => $message,
            'code' => $code,
            'timestamp' => date('c')
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Log API request/response (for debugging and monitoring)
     *
     * In production, this should write to a log file or logging service
     *
     * @param string $action Action being performed
     * @param array $data Additional data to log
     * @return void
     */
    protected function log(string $action, array $data = []): void {
        // In production, implement proper logging
        // For now, we'll skip to avoid performance impact

        // Example with error_log:
        // error_log(json_encode([
        //     'timestamp' => date('c'),
        //     'action' => $action,
        //     'method' => $this->method,
        //     'ip' => $this->getClientIP(),
        //     'data' => $data
        // ]));
    }

    /**
     * Abstract method that must be implemented by child classes
     *
     * This is where the main API logic goes
     *
     * @return void
     */
    abstract public function handleRequest(): void;
}
