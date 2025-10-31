<?php
/**
 * PYRAMEDIA Admin - Configuration
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

// Admin settings
define('ADMIN_TITLE', 'PYRAMEDIA Admin');
define('ADMIN_VERSION', '1.0.0');
define('ADMIN_PER_PAGE', 20);

// Upload settings
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('UPLOAD_PATH', __DIR__ . '/../../uploads/blog/');
define('UPLOAD_URL', '/uploads/blog/');

// TinyMCE API Key (Get free key from: https://www.tiny.cloud/)
define('TINYMCE_API_KEY', 'n9rgzrm86u06e51cwwdhps1qq4vs2i2v2hnz68r18r35syl2');

// Timezone
date_default_timezone_set('Asia/Dubai');

// Error reporting (disable in production)
if (defined('ADMIN_DEBUG') && ADMIN_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../../logs/admin_errors.log');
}

/**
 * Helper function to format file size
 */
function formatFileSize(int $bytes): string {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Helper function to sanitize filename
 */
function sanitizeFilename(string $filename): string {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    return strtolower($filename);
}

/**
 * Helper function to generate unique filename
 */
function generateUniqueFilename(string $originalFilename): string {
    $ext = pathinfo($originalFilename, PATHINFO_EXTENSION);
    $name = pathinfo($originalFilename, PATHINFO_FILENAME);
    $name = sanitizeFilename($name);
    return $name . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
}

/**
 * Helper function to get time ago
 */
function timeAgo(string $datetime): string {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

/**
 * Helper function to truncate text
 */
function truncate(string $text, int $length = 100): string {
    $text = strip_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . '...';
}

/**
 * Helper function to escape HTML
 */
function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Helper function to check if current page matches
 */
function isCurrentPage(string $page): bool {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page;
}
