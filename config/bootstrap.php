<?php
/**
 * PYRAMEDIA - Bootstrap Configuration
 *
 * This file is automatically loaded by Composer's autoloader.
 * It initializes the environment configuration and loads .env variables.
 *
 * @package PYRAMEDIA
 * @since 1.0.0
 */

// Load Composer's autoloader
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;

    // Load environment variables
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    // Validate required environment variables
    $dotenv->required([
        'DB_HOST',
        'DB_NAME',
        'DB_USER',
        'DB_PASS'
    ])->notEmpty();

    // Optional: Set default values for missing variables
    if (!isset($_ENV['APP_ENV'])) {
        $_ENV['APP_ENV'] = 'production';
    }

    if (!isset($_ENV['APP_DEBUG'])) {
        $_ENV['APP_DEBUG'] = 'false';
    }
} else {
    // Fallback for systems without Composer
    // This ensures the application still works if dependencies aren't installed
    if (!function_exists('env')) {
        /**
         * Get environment variable with fallback
         *
         * @param string $key Environment variable key
         * @param mixed $default Default value if not found
         * @return mixed
         */
        function env($key, $default = null) {
            $value = getenv($key);

            if ($value === false) {
                return $default;
            }

            // Convert string boolean values
            switch (strtolower($value)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
            }

            return $value;
        }
    }
}

/**
 * Helper function to get environment variable (if not already defined by phpdotenv)
 *
 * @param string $key Environment variable key
 * @param mixed $default Default value if not found
 * @return mixed
 */
if (!function_exists('env')) {
    function env($key, $default = null) {
        // Try $_ENV first (phpdotenv)
        if (isset($_ENV[$key])) {
            $value = $_ENV[$key];
        }
        // Fall back to $_SERVER
        elseif (isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        }
        // Fall back to getenv()
        else {
            $value = getenv($key);
            if ($value === false) {
                return $default;
            }
        }

        // Convert string boolean values
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}

// Set error reporting based on environment
if (env('APP_DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// Set timezone (optional, adjust as needed)
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));
