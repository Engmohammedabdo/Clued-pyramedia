<?php
/**
 * PYRAMEDIA Admin - Authentication System
 * Modern PHP 8+ authentication with security best practices
 */

declare(strict_types=1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

class Auth {
    private Database $db;
    private const SESSION_LIFETIME = 28800; // 8 hours
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_TIMEOUT = 900; // 15 minutes

    public function __construct() {
        $this->db = getDB();
        $this->checkSessionTimeout();
    }

    /**
     * Login user with email and password
     */
    public function login(string $email, string $password): array {
        try {
            // Check login attempts
            if ($this->isLoginBlocked($email)) {
                return [
                    'success' => false,
                    'error' => 'Too many login attempts. Please try again in 15 minutes.'
                ];
            }

            // Get user
            $stmt = $this->db->getConnection()->prepare("
                SELECT id, full_name, email, password, role, avatar
                FROM users
                WHERE email = :email AND status = 'active'
                LIMIT 1
            ");

            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $this->recordFailedLogin($email);
                return [
                    'success' => false,
                    'error' => 'Invalid email or password'
                ];
            }

            // Check if user has admin/author role
            if (!in_array($user['role'], ['admin', 'author'])) {
                return [
                    'success' => false,
                    'error' => 'Access denied. Admin or author role required.'
                ];
            }

            // Login successful - clear failed attempts
            $this->clearFailedLogins($email);

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();

            // Update last login
            $this->updateLastLogin($user['id']);

            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ];

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred. Please try again.'
            ];
        }
    }

    /**
     * Logout current user
     */
    public function logout(): void {
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) &&
               isset($_SESSION['login_time']) &&
               (time() - $_SESSION['login_time']) < self::SESSION_LIFETIME;
    }

    /**
     * Get current user data
     */
    public function getCurrentUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['user_role'] ?? null,
            'avatar' => $_SESSION['user_avatar'] ?? null
        ];
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool {
        return $this->isLoggedIn() && ($_SESSION['user_role'] ?? '') === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool {
        return $this->hasRole('admin');
    }

    /**
     * Require login (redirect to login page if not logged in)
     */
    public function requireLogin(): void {
        if (!$this->isLoggedIn()) {
            header('Location: index.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    /**
     * Require admin role
     */
    public function requireAdmin(): void {
        $this->requireLogin();

        if (!$this->isAdmin()) {
            http_response_code(403);
            die('Access denied. Admin role required.');
        }
    }

    /**
     * Check session timeout
     */
    private function checkSessionTimeout(): void {
        if (isset($_SESSION['last_activity'])) {
            $timeout = 3600; // 1 hour of inactivity

            if (time() - $_SESSION['last_activity'] > $timeout) {
                $this->logout();
                header('Location: index.php?timeout=1');
                exit;
            }
        }

        $_SESSION['last_activity'] = time();
    }

    /**
     * Check if login is blocked due to failed attempts
     */
    private function isLoginBlocked(string $email): bool {
        $cacheFile = sys_get_temp_dir() . '/login_attempts_' . md5($email);

        if (!file_exists($cacheFile)) {
            return false;
        }

        $data = json_decode(file_get_contents($cacheFile), true);

        if (!$data || !isset($data['attempts'], $data['timestamp'])) {
            return false;
        }

        // Check if timeout period has passed
        if (time() - $data['timestamp'] > self::LOGIN_TIMEOUT) {
            unlink($cacheFile);
            return false;
        }

        return $data['attempts'] >= self::MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Record failed login attempt
     */
    private function recordFailedLogin(string $email): void {
        $cacheFile = sys_get_temp_dir() . '/login_attempts_' . md5($email);

        $data = ['attempts' => 1, 'timestamp' => time()];

        if (file_exists($cacheFile)) {
            $existing = json_decode(file_get_contents($cacheFile), true);
            if ($existing && isset($existing['attempts'])) {
                $data['attempts'] = $existing['attempts'] + 1;
            }
        }

        file_put_contents($cacheFile, json_encode($data));
    }

    /**
     * Clear failed login attempts
     */
    private function clearFailedLogins(string $email): void {
        $cacheFile = sys_get_temp_dir() . '/login_attempts_' . md5($email);

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin(int $userId): void {
        try {
            $stmt = $this->db->getConnection()->prepare("
                UPDATE users
                SET last_login = NOW()
                WHERE id = :id
            ");
            $stmt->execute(['id' => $userId]);
        } catch (Exception $e) {
            error_log("Failed to update last login: " . $e->getMessage());
        }
    }

    /**
     * Generate CSRF token
     */
    public function generateCsrfToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public function verifyCsrfToken(string $token): bool {
        return isset($_SESSION['csrf_token']) &&
               hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Change password
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array {
        try {
            // Get current password hash
            $stmt = $this->db->getConnection()->prepare("
                SELECT password FROM users WHERE id = :id
            ");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                return [
                    'success' => false,
                    'error' => 'Current password is incorrect'
                ];
            }

            // Validate new password
            if (strlen($newPassword) < 8) {
                return [
                    'success' => false,
                    'error' => 'New password must be at least 8 characters'
                ];
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);

            $stmt = $this->db->getConnection()->prepare("
                UPDATE users SET password = :password WHERE id = :id
            ");
            $stmt->execute([
                'password' => $hashedPassword,
                'id' => $userId
            ]);

            return ['success' => true];

        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred. Please try again.'
            ];
        }
    }
}

/**
 * Get Auth instance
 */
function getAuth(): Auth {
    static $auth = null;

    if ($auth === null) {
        $auth = new Auth();
    }

    return $auth;
}
