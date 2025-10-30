<?php
/**
 * PYRAMEDIA - Contact Form Handler
 * Handles both contact form and service inquiry submissions
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

// Determine form type
$formType = isset($_POST['form_type']) ? $_POST['form_type'] : 'service';

// Extract and sanitize data
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) :
        (isset($_POST['inquiry_name']) ? sanitizeInput($_POST['inquiry_name']) : '');
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) :
         (isset($_POST['inquiry_email']) ? sanitizeInput($_POST['inquiry_email']) : '');
$phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) :
         (isset($_POST['inquiry_phone']) ? sanitizeInput($_POST['inquiry_phone']) : '');
$message = isset($_POST['message']) ? sanitizeInput($_POST['message']) :
           (isset($_POST['inquiry_details']) ? sanitizeInput($_POST['inquiry_details']) : '');

// Validate required fields
if (empty($name) || empty($email)) {
    sendResponse(false, 'Name and email are required');
}

if (!validateEmail($email)) {
    sendResponse(false, 'Invalid email address');
}

// Service-specific data
$service = isset($_POST['service']) ? sanitizeInput($_POST['service']) : null;
$serviceSpecificData = [];

if ($service) {
    // Extract service-specific fields
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['inquiry_name', 'inquiry_email', 'inquiry_phone', 'inquiry_details', 'service'])) {
            $serviceSpecificData[$key] = sanitizeInput($value);
        }
    }
}

// Prepare email content
$to = "info@pyramedia.ae"; // Replace with your actual email
$subject = $service ? "New Service Inquiry: " . ucwords(str_replace('-', ' ', $service)) : "New Contact Form Submission";

$emailBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #FF6B35; }
        .value { margin-top: 5px; padding: 10px; background: white; border-left: 3px solid #FF6B35; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>🔔 New " . ($service ? "Service Inquiry" : "Contact") . "</h2>
        </div>
        <div class='content'>
            <div class='field'>
                <div class='label'>Name:</div>
                <div class='value'>{$name}</div>
            </div>
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'>{$email}</div>
            </div>
            <div class='field'>
                <div class='label'>Phone:</div>
                <div class='value'>{$phone}</div>
            </div>";

if ($service) {
    $emailBody .= "
            <div class='field'>
                <div class='label'>Service Interested In:</div>
                <div class='value'>" . ucwords(str_replace('-', ' ', $service)) . "</div>
            </div>";

    foreach ($serviceSpecificData as $key => $value) {
        $emailBody .= "
            <div class='field'>
                <div class='label'>" . ucwords(str_replace('_', ' ', $key)) . ":</div>
                <div class='value'>{$value}</div>
            </div>";
    }
}

if (!empty($message)) {
    $emailBody .= "
            <div class='field'>
                <div class='label'>Message:</div>
                <div class='value'>{$message}</div>
            </div>";
}

$emailBody .= "
            <div class='field'>
                <div class='label'>Submitted On:</div>
                <div class='value'>" . date('F j, Y \a\t g:i A') . "</div>
            </div>
        </div>
        <div class='footer'>
            <p>This email was sent from PYRAMEDIA website contact form</p>
        </div>
    </div>
</body>
</html>
";

// Email headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: PYRAMEDIA <noreply@pyramedia.ae>" . "\r\n";
$headers .= "Reply-To: {$email}" . "\r\n";

// Send email
$emailSent = mail($to, $subject, $emailBody, $headers);

// Send confirmation email to user
$userSubject = "Thank you for contacting PYRAMEDIA";
$userEmailBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Thank You, {$name}!</h1>
        </div>
        <div class='content'>
            <p>We've received your " . ($service ? "inquiry about our <strong>" . ucwords(str_replace('-', ' ', $service)) . "</strong> service" : "message") . " and we're excited to help you!</p>
            <p>Our team will review your request and get back to you within 24 hours.</p>
            <p>In the meantime, feel free to explore our website and learn more about how we can help transform your digital presence.</p>
            <div style='text-align: center;'>
                <a href='https://pyramedia.ae' class='button'>Visit Our Website</a>
            </div>
            <p style='margin-top: 30px;'>Best regards,<br><strong>The PYRAMEDIA Team</strong></p>
        </div>
    </div>
</body>
</html>
";

$userHeaders = "MIME-Version: 1.0" . "\r\n";
$userHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$userHeaders .= "From: PYRAMEDIA <info@pyramedia.ae>" . "\r\n";

mail($email, $userSubject, $userEmailBody, $userHeaders);

// n8n Webhook Integration (Optional)
$webhookUrl = "https://your-n8n-instance.com/webhook/pyramedia-contact"; // Replace with your n8n webhook URL

$webhookData = [
    'type' => $service ? 'service_inquiry' : 'contact',
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'message' => $message,
    'service' => $service,
    'service_data' => $serviceSpecificData,
    'submitted_at' => date('c'),
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
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

// Log submission (optional - create logs directory first)
/*
$logEntry = date('Y-m-d H:i:s') . " | {$name} | {$email} | " . ($service ?? 'contact') . "\n";
file_put_contents('../logs/contact_log.txt', $logEntry, FILE_APPEND);
*/

if ($emailSent) {
    sendResponse(true, 'Your message has been sent successfully!', [
        'name' => $name,
        'email' => $email
    ]);
} else {
    sendResponse(false, 'Failed to send message. Please try again later.');
}
?>
