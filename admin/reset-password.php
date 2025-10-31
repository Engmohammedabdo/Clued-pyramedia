<?php
/**
 * PYRAMEDIA - Password Reset Tool
 * Use this to reset admin password if you're locked out
 *
 * IMPORTANT: Delete this file after use for security!
 */

// Configuration
$newPassword = 'admin123'; // Change this to your desired password
$userEmail = 'admin@pyramedia.ae'; // Email of the account to reset

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - PYRAMEDIA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            max-width: 600px;
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
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #FF6B35;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 13px;
        }
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .credentials p {
            margin: 8px 0;
            font-size: 14px;
        }
        .credentials strong {
            color: #FF6B35;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Password Reset Tool</h1>
        <p class="subtitle">PYRAMEDIA Admin Panel</p>

        <div class="warning">
            <strong>⚠️ SECURITY WARNING:</strong><br>
            Delete this file immediately after resetting your password!
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            try {
                // Load database
                require_once __DIR__ . '/../config/database.php';
                $db = new Database();
                $conn = $db->getConnection();

                if (!$conn) {
                    throw new Exception('Database connection failed');
                }

                // Check if user exists
                $stmt = $conn->prepare("SELECT id, email, full_name FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    echo '<div class="error">❌ User with email <strong>' . htmlspecialchars($email) . '</strong> not found!</div>';
                } else {
                    // Hash the new password using Argon2ID (most secure)
                    if (defined('PASSWORD_ARGON2ID')) {
                        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
                    } else {
                        // Fallback to bcrypt if Argon2ID not available
                        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    }

                    // Update password
                    $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
                    $updateStmt->execute([
                        'password' => $hashedPassword,
                        'id' => $user['id']
                    ]);

                    echo '<div class="success">✅ <strong>Password Reset Successful!</strong></div>';
                    echo '<div class="credentials">';
                    echo '<p><strong>Name:</strong> ' . htmlspecialchars($user['full_name']) . '</p>';
                    echo '<p><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</p>';
                    echo '<p><strong>New Password:</strong> ' . htmlspecialchars($password) . '</p>';
                    echo '</div>';
                    echo '<div class="info">';
                    echo '<strong>Next Steps:</strong><br>';
                    echo '1. <a href="index.php">Go to Login Page</a><br>';
                    echo '2. Login with the credentials above<br>';
                    echo '3. <strong>DELETE THIS FILE (reset-password.php) immediately for security!</strong>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="error">❌ <strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            // Show form
            ?>
            <div class="info">
                <strong>📝 Instructions:</strong><br>
                1. Enter the email of the account you want to reset<br>
                2. Enter your desired new password<br>
                3. Click "Reset Password"<br>
                4. <strong>Delete this file after use!</strong>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars($userEmail) ?>"
                        required
                        placeholder="admin@pyramedia.ae"
                    >
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input
                        type="text"
                        name="password"
                        value="<?= htmlspecialchars($newPassword) ?>"
                        required
                        placeholder="Enter new password"
                        minlength="6"
                    >
                    <small style="color: #666; font-size: 12px;">Minimum 6 characters</small>
                </div>

                <button type="submit" class="btn">🔄 Reset Password</button>
            </form>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                <p style="font-size: 14px; color: #666;">
                    <strong>Default Credentials (if database was just imported):</strong><br>
                    Email: <code>admin@pyramedia.ae</code><br>
                    Password: <code>admin123</code>
                </p>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
