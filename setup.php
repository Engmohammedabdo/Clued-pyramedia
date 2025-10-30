<?php
/**
 * PYRAMEDIA - Database Setup Script
 * Run this file ONCE to create database tables
 *
 * Usage: Visit this file in browser: https://your-domain.com/setup.php
 * After successful setup, DELETE this file for security!
 */

// Include database configuration
require_once 'config/database.php';

// Set longer execution time for large schemas
set_time_limit(300);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PYRAMEDIA - Database Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #FF6B35;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .status {
            background: #f8f9fa;
            border-left: 4px solid #FF6B35;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        button {
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        button:hover {
            transform: scale(1.05);
        }
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .step {
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .step-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .step-result {
            color: #666;
            font-size: 14px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #FF6B35;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 PYRAMEDIA Database Setup</h1>
        <p class="subtitle">Setting up database tables for your blog system</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
            echo '<div id="progress">';

            try {
                $db = getDB();

                if (!$db->conn) {
                    throw new Exception('Failed to connect to database. Please check your credentials in config/database.php');
                }

                echo '<div class="status success">✓ Database connection successful!</div>';

                // Read SQL file
                $sqlFile = 'sql/blog_schema.sql';
                if (!file_exists($sqlFile)) {
                    throw new Exception('SQL file not found: ' . $sqlFile);
                }

                $sql = file_get_contents($sqlFile);

                // Split into individual statements
                $statements = array_filter(
                    array_map('trim', explode(';', $sql)),
                    function($stmt) {
                        return !empty($stmt) &&
                               strpos($stmt, '--') !== 0 &&
                               strpos($stmt, '/*') !== 0;
                    }
                );

                $successCount = 0;
                $errorCount = 0;

                echo '<div class="status info">📊 Executing SQL statements...</div>';

                foreach ($statements as $statement) {
                    // Skip comments and empty statements
                    if (empty(trim($statement)) ||
                        strpos(trim($statement), '--') === 0 ||
                        strpos(trim($statement), '/*') === 0) {
                        continue;
                    }

                    try {
                        $db->conn->exec($statement);
                        $successCount++;
                    } catch (PDOException $e) {
                        // Ignore "table already exists" errors
                        if (strpos($e->getMessage(), 'already exists') === false) {
                            $errorCount++;
                            echo '<div class="step">';
                            echo '<div class="step-title">⚠️ Warning:</div>';
                            echo '<div class="step-result">' . htmlspecialchars($e->getMessage()) . '</div>';
                            echo '</div>';
                        }
                    }
                }

                echo '<div class="status success">';
                echo "✓ Setup completed successfully!<br>";
                echo "✓ Executed {$successCount} SQL statements<br>";
                if ($errorCount > 0) {
                    echo "⚠ {$errorCount} warnings (likely tables already exist)";
                }
                echo '</div>';

                echo '<div class="status warning">';
                echo '<strong>🔒 IMPORTANT SECURITY NOTICE:</strong><br>';
                echo 'Please <strong>DELETE this setup.php file</strong> immediately for security reasons!<br>';
                echo 'Your database is now ready to use.';
                echo '</div>';

                echo '<div class="status info">';
                echo '<strong>📝 Next Steps:</strong><br>';
                echo '1. Delete setup.php file<br>';
                echo '2. Visit your admin panel: <code>/admin/login.php</code><br>';
                echo '3. Default admin login:<br>';
                echo '   - Username: <code>admin</code><br>';
                echo '   - Password: <code>admin123</code> (Change this immediately!)<br>';
                echo '</div>';

            } catch (Exception $e) {
                echo '<div class="status error">';
                echo '✗ Setup failed: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }

            echo '</div>';
        } else {
            ?>
            <div class="status info">
                <strong>ℹ️ Before you start:</strong><br>
                Make sure you have:
                <ul style="margin: 10px 0 0 20px;">
                    <li>Created the database: <code>pyramed1_final</code></li>
                    <li>Updated database credentials in <code>config/database.php</code></li>
                    <li>Backed up any existing data (if applicable)</li>
                </ul>
            </div>

            <div class="status warning">
                <strong>⚠️ Warning:</strong><br>
                This will create all necessary database tables. If tables already exist, they will be preserved with their data.
            </div>

            <form method="POST" style="margin-top: 30px;">
                <button type="submit" name="setup" value="1">
                    🚀 Start Database Setup
                </button>
            </form>
            <?php
        }
        ?>
    </div>
</body>
</html>
