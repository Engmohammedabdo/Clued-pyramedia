<?php
/**
 * PYRAMEDIA - Server Diagnostic Tool
 * Upload this file and access it to check your server configuration
 */

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>PYRAMEDIA Server Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #FF6B35; border-bottom: 3px solid #FF6B35; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .check { color: #28a745; font-weight: bold; }
        .cross { color: #dc3545; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔍 PYRAMEDIA Server Diagnostic</h1>";
echo "<p><strong>Generated:</strong> " . date('Y-m-d H:i:s') . "</p>";

$errors = [];
$warnings = [];
$success = [];

// ==========================================
// 1. PHP VERSION CHECK
// ==========================================
echo "<h2>1. PHP Version</h2>";
$phpVersion = phpversion();
echo "<div class='info'>Current PHP Version: <strong>$phpVersion</strong></div>";

if (version_compare($phpVersion, '8.0.0', '>=')) {
    echo "<div class='success'>✓ PHP version is compatible (8.0+)</div>";
    $success[] = "PHP version OK";
} else {
    echo "<div class='error'>✗ PHP version is TOO OLD. Required: 8.0+, Current: $phpVersion</div>";
    echo "<div class='warning'><strong>FIX:</strong> In cPanel → Select PHP Version → Choose PHP 8.0, 8.1, or 8.2</div>";
    $errors[] = "PHP version too old";
}

// ==========================================
// 2. REQUIRED PHP EXTENSIONS
// ==========================================
echo "<h2>2. PHP Extensions</h2>";
echo "<table>";
echo "<tr><th>Extension</th><th>Status</th><th>Required For</th></tr>";

$requiredExtensions = [
    'pdo' => 'Database connection',
    'pdo_mysql' => 'MySQL database',
    'gd' => 'Image processing',
    'json' => 'JSON handling',
    'mbstring' => 'Multibyte strings',
    'session' => 'User sessions',
    'fileinfo' => 'File type detection'
];

foreach ($requiredExtensions as $ext => $purpose) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? "<span class='check'>✓ Loaded</span>" : "<span class='cross'>✗ Missing</span>";
    $class = $loaded ? '' : " class='error'";
    echo "<tr$class><td>$ext</td><td>$status</td><td>$purpose</td></tr>";

    if (!$loaded) {
        $errors[] = "Missing extension: $ext";
    }
}
echo "</table>";

// ==========================================
// 3. DIRECTORY STRUCTURE
// ==========================================
echo "<h2>3. Directory Structure</h2>";
echo "<table>";
echo "<tr><th>Directory/File</th><th>Exists</th><th>Writable</th><th>Permissions</th></tr>";

$basePath = dirname(__DIR__);
$checkPaths = [
    '/config' => true,
    '/config/database.php' => false,
    '/admin' => true,
    '/admin/includes' => true,
    '/admin/includes/auth.php' => false,
    '/admin/includes/config.php' => false,
    '/uploads' => true,
    '/uploads/blog' => true,
    '/logs' => true
];

foreach ($checkPaths as $path => $needsWritable) {
    $fullPath = $basePath . $path;
    $exists = file_exists($fullPath);
    $writable = $exists && is_writable($fullPath);
    $perms = $exists ? substr(sprintf('%o', fileperms($fullPath)), -4) : 'N/A';

    $existsStatus = $exists ? "<span class='check'>✓</span>" : "<span class='cross'>✗</span>";
    $writableStatus = $needsWritable ? ($writable ? "<span class='check'>✓</span>" : "<span class='cross'>✗</span>") : 'N/A';

    $class = (!$exists || ($needsWritable && !$writable)) ? " class='error'" : '';

    echo "<tr$class><td>$path</td><td>$existsStatus</td><td>$writableStatus</td><td>$perms</td></tr>";

    if (!$exists) {
        $errors[] = "Missing: $path";
    } elseif ($needsWritable && !$writable) {
        $warnings[] = "Not writable: $path";
    }
}
echo "</table>";

// ==========================================
// 4. DATABASE CONNECTION
// ==========================================
echo "<h2>4. Database Connection</h2>";

$dbConfigPath = $basePath . '/config/database.php';
if (file_exists($dbConfigPath)) {
    echo "<div class='success'>✓ Database config file exists</div>";

    try {
        require_once $dbConfigPath;

        // Try to get database instance
        $db = getDB();

        if ($db && $db->conn) {
            echo "<div class='success'>✓ Database connection SUCCESSFUL</div>";

            // Check if tables exist
            $tables = ['users', 'blog_posts', 'blog_categories', 'blog_tags', 'media_library'];
            echo "<h3>Database Tables:</h3>";
            echo "<table>";
            echo "<tr><th>Table</th><th>Status</th></tr>";

            foreach ($tables as $table) {
                try {
                    $stmt = $db->conn->query("SELECT COUNT(*) FROM `$table`");
                    $count = $stmt->fetchColumn();
                    echo "<tr><td>$table</td><td><span class='check'>✓ Exists</span> ($count records)</td></tr>";
                } catch (Exception $e) {
                    echo "<tr class='error'><td>$table</td><td><span class='cross'>✗ Missing or error</span></td></tr>";
                    $errors[] = "Missing table: $table";
                }
            }
            echo "</table>";

        } else {
            echo "<div class='error'>✗ Database connection FAILED</div>";
            $errors[] = "Database connection failed";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        $errors[] = "Database error: " . $e->getMessage();
    }
} else {
    echo "<div class='error'>✗ Database config file NOT FOUND: $dbConfigPath</div>";
    $errors[] = "Database config missing";
}

// ==========================================
// 5. FILE PERMISSIONS
// ==========================================
echo "<h2>5. Recommended Permissions</h2>";
echo "<div class='info'>";
echo "<pre>
Directories:    755 (rwxr-xr-x)
PHP Files:      644 (rw-r--r--)
Upload folders: 755 or 775 (if 755 doesn't work)
Config folder:  755
</pre>";
echo "</div>";

// ==========================================
// 6. SERVER INFORMATION
// ==========================================
echo "<h2>6. Server Information</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>Server Software</td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</td></tr>";
echo "<tr><td>PHP SAPI</td><td>" . php_sapi_name() . "</td></tr>";
echo "<tr><td>Document Root</td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</td></tr>";
echo "<tr><td>Script Path</td><td>" . __FILE__ . "</td></tr>";
echo "<tr><td>Upload Max Filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>Post Max Size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>Max Execution Time</td><td>" . ini_get('max_execution_time') . "s</td></tr>";
echo "<tr><td>Memory Limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td>Timezone</td><td>" . date_default_timezone_get() . "</td></tr>";
echo "</table>";

// ==========================================
// 7. SUMMARY
// ==========================================
echo "<h2>7. Summary</h2>";

if (empty($errors)) {
    echo "<div class='success'><strong>✓ ALL CHECKS PASSED!</strong> Your server is configured correctly.</div>";
    echo "<div class='info'>You can now access the admin panel at: <a href='index.php'>index.php</a></div>";
} else {
    echo "<div class='error'><strong>✗ ERRORS FOUND:</strong></div>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
}

if (!empty($warnings)) {
    echo "<div class='warning'><strong>⚠ WARNINGS:</strong></div>";
    echo "<ul>";
    foreach ($warnings as $warning) {
        echo "<li>$warning</li>";
    }
    echo "</ul>";
}

// ==========================================
// 8. QUICK FIXES
// ==========================================
echo "<h2>8. Quick Fixes</h2>";
echo "<div class='info'>";
echo "<h3>If you see errors above:</h3>";
echo "<ol>";
echo "<li><strong>PHP Version:</strong> cPanel → Select PHP Version → Choose 8.0+</li>";
echo "<li><strong>Missing Extensions:</strong> cPanel → Select PHP Version → Check boxes for required extensions</li>";
echo "<li><strong>Missing Directories:</strong> Create them via File Manager</li>";
echo "<li><strong>Permission Issues:</strong> Right-click folder → Change Permissions → Set to 755</li>";
echo "<li><strong>Database Connection:</strong> Verify credentials in config/database.php</li>";
echo "<li><strong>Missing Tables:</strong> Import sql/blog_schema.sql via phpMyAdmin</li>";
echo "</ol>";
echo "</div>";

echo "</div></body></html>";
?>
