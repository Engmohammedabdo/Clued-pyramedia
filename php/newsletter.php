<?php
/**
 * PYRAMEDIA - Newsletter Subscription Handler
 * Handles newsletter email subscriptions
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Response function
function sendResponse($success, $message, $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Validation function
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

// Extract and sanitize email
$email = isset($_POST['newsletter_email']) ? sanitizeInput($_POST['newsletter_email']) : '';

// Validate email
if (empty($email)) {
    sendResponse(false, 'Email address is required');
}

if (!validateEmail($email)) {
    sendResponse(false, 'Invalid email address');
}

// Check if already subscribed (simple text file check - use database in production)
$subscribersFile = '../data/newsletter_subscribers.txt';

// Create data directory if it doesn't exist
if (!file_exists('../data')) {
    mkdir('../data', 0755, true);
}

// Check for duplicate subscription
if (file_exists($subscribersFile)) {
    $subscribers = file($subscribersFile, FILE_IGNORE_NEW_LINES);
    if (in_array($email, $subscribers)) {
        sendResponse(false, 'This email is already subscribed to our newsletter');
    }
}

// Add to subscribers list
file_put_contents($subscribersFile, $email . "\n", FILE_APPEND);

// Generate unique subscriber ID
$subscriberId = substr(md5($email . time()), 0, 10);

// Send welcome email
$to = $email;
$subject = "Welcome to PYRAMEDIA Newsletter! 🎉";

$emailBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 40px 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 40px 30px; border-radius: 0 0 10px 10px; }
        .welcome-box { background: white; padding: 30px; border-radius: 10px; margin: 20px 0; text-align: center; border: 2px solid #FF6B35; }
        .benefit-item { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #FF6B35; border-radius: 5px; }
        .button { display: inline-block; background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 10px; font-weight: bold; }
        .social-links { text-align: center; margin: 30px 0; }
        .social-icon { display: inline-block; margin: 0 10px; width: 40px; height: 40px; background: #FF6B35; color: white; border-radius: 50%; line-height: 40px; text-align: center; text-decoration: none; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1 style='margin: 0; font-size: 36px;'>🎉 Welcome to PYRAMEDIA!</h1>
            <p style='margin: 10px 0 0 0; font-size: 18px;'>You're now part of our exclusive community</p>
        </div>
        <div class='content'>
            <div class='welcome-box'>
                <h2 style='color: #FF6B35; margin-top: 0;'>Thank You for Subscribing!</h2>
                <p style='font-size: 16px; line-height: 1.8;'>
                    We're thrilled to have you join our newsletter family. Get ready to receive the latest insights,
                    trends, and exclusive content about digital marketing, automation, and innovation.
                </p>
            </div>

            <h3 style='color: #FF6B35; margin-top: 30px;'>What You'll Get:</h3>

            <div class='benefit-item'>
                <strong>📊 Industry Insights</strong>
                <p style='margin: 5px 0 0 0;'>Latest trends and data-driven insights in digital marketing and automation</p>
            </div>

            <div class='benefit-item'>
                <strong>💡 Expert Tips</strong>
                <p style='margin: 5px 0 0 0;'>Practical strategies and tips from our marketing experts</p>
            </div>

            <div class='benefit-item'>
                <strong>🎁 Exclusive Offers</strong>
                <p style='margin: 5px 0 0 0;'>Special discounts and early access to our services</p>
            </div>

            <div class='benefit-item'>
                <strong>📚 Case Studies</strong>
                <p style='margin: 5px 0 0 0;'>Real success stories and proven strategies from our clients</p>
            </div>

            <div class='benefit-item'>
                <strong>🚀 Product Updates</strong>
                <p style='margin: 5px 0 0 0;'>Be the first to know about new services and features</p>
            </div>

            <div style='text-align: center; margin: 40px 0;'>
                <h3 style='color: #FF6B35;'>Ready to Transform Your Digital Presence?</h3>
                <p>Book a free consultation with our experts today!</p>
                <a href='https://pyramedia.ae#contact' class='button'>Book Free Consultation</a>
                <a href='https://pyramedia.ae#services' class='button' style='background: white; color: #FF6B35; border: 2px solid #FF6B35;'>Explore Services</a>
            </div>

            <div class='social-links'>
                <p style='font-weight: bold; color: #FF6B35;'>Follow Us on Social Media:</p>
                <a href='https://facebook.com/pyramedia' class='social-icon'>f</a>
                <a href='https://twitter.com/pyramedia' class='social-icon'>t</a>
                <a href='https://instagram.com/pyramedia' class='social-icon'>i</a>
                <a href='https://linkedin.com/company/pyramedia' class='social-icon'>in</a>
            </div>

            <div style='background: #fff; padding: 20px; border-radius: 10px; margin-top: 30px; text-align: center;'>
                <p style='margin: 0; color: #666;'>
                    <strong>Pro Tip:</strong> Add info@pyramedia.ae to your contacts to ensure you never miss our emails!
                </p>
            </div>

            <div class='footer'>
                <p><strong>PYRAMEDIA</strong> - Marketing & Media Solutions</p>
                <p>Dubai, UAE | info@pyramedia.ae | +971 XX XXX XXXX</p>
                <p style='margin-top: 20px;'>
                    You're receiving this email because you subscribed to our newsletter.<br>
                    <a href='https://pyramedia.ae/unsubscribe?id={$subscriberId}' style='color: #FF6B35;'>Unsubscribe</a> |
                    <a href='https://pyramedia.ae/privacy' style='color: #FF6B35;'>Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
";

// Email headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: PYRAMEDIA <info@pyramedia.ae>" . "\r\n";
$headers .= "Reply-To: info@pyramedia.ae" . "\r\n";

// Send welcome email
$emailSent = mail($to, $subject, $emailBody, $headers);

// Send notification to admin
$adminEmail = "info@pyramedia.ae";
$adminSubject = "New Newsletter Subscription";
$adminBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .info-box { background: white; padding: 20px; border-left: 4px solid #FF6B35; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>📧 New Newsletter Subscription</h2>
        </div>
        <div class='content'>
            <div class='info-box'>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Subscriber ID:</strong> {$subscriberId}</p>
                <p><strong>Subscribed On:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                <p><strong>IP Address:</strong> " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "</p>
            </div>
        </div>
    </div>
</body>
</html>
";

$adminHeaders = "MIME-Version: 1.0" . "\r\n";
$adminHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$adminHeaders .= "From: PYRAMEDIA Newsletter <noreply@pyramedia.ae>" . "\r\n";

mail($adminEmail, $adminSubject, $adminBody, $adminHeaders);

// n8n Webhook Integration
$webhookUrl = "https://your-n8n-instance.com/webhook/pyramedia-newsletter"; // Replace with your n8n webhook URL

$webhookData = [
    'email' => $email,
    'subscriber_id' => $subscriberId,
    'subscribed_at' => date('c'),
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
];

// Send to n8n (uncomment when ready to use)
/*
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$webhookResponse = curl_exec($ch);
curl_close($ch);
*/

// Log subscription (optional - create logs directory first)
/*
$logEntry = date('Y-m-d H:i:s') . " | {$email} | {$subscriberId}\n";
file_put_contents('../logs/newsletter_log.txt', $logEntry, FILE_APPEND);
*/

// Save subscriber data (JSON format for better data management)
$subscriberData = [
    'email' => $email,
    'subscriber_id' => $subscriberId,
    'subscribed_at' => date('c'),
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'status' => 'active'
];

$subscribersDataFile = '../data/subscribers_data.json';
$allSubscribers = [];

if (file_exists($subscribersDataFile)) {
    $allSubscribers = json_decode(file_get_contents($subscribersDataFile), true) ?? [];
}

$allSubscribers[] = $subscriberData;
file_put_contents($subscribersDataFile, json_encode($allSubscribers, JSON_PRETTY_PRINT));

if ($emailSent) {
    sendResponse(true, 'Successfully subscribed! Check your email for a welcome message.', [
        'email' => $email,
        'subscriber_id' => $subscriberId
    ]);
} else {
    // Still save the subscription even if email fails
    sendResponse(true, 'Successfully subscribed!', [
        'email' => $email,
        'subscriber_id' => $subscriberId
    ]);
}
?>
