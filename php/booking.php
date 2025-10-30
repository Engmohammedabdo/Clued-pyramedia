<?php
/**
 * PYRAMEDIA - Booking System Handler
 * Handles consultation booking appointments
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

// Extract and sanitize data
$clientName = isset($_POST['client_name']) ? sanitizeInput($_POST['client_name']) : '';
$clientEmail = isset($_POST['client_email']) ? sanitizeInput($_POST['client_email']) : '';
$clientPhone = isset($_POST['client_phone']) ? sanitizeInput($_POST['client_phone']) : '';
$companyName = isset($_POST['company_name']) ? sanitizeInput($_POST['company_name']) : '';
$helpDescription = isset($_POST['help_description']) ? sanitizeInput($_POST['help_description']) : '';
$appointmentDate = isset($_POST['date']) ? sanitizeInput($_POST['date']) : '';
$appointmentTime = isset($_POST['time']) ? sanitizeInput($_POST['time']) : '';

// Validate required fields
if (empty($clientName) || empty($clientEmail) || empty($clientPhone) || empty($appointmentDate) || empty($appointmentTime)) {
    sendResponse(false, 'All required fields must be filled');
}

if (!validateEmail($clientEmail)) {
    sendResponse(false, 'Invalid email address');
}

// Validate date is in the future
$selectedDate = new DateTime($appointmentDate);
$today = new DateTime('today');

if ($selectedDate < $today) {
    sendResponse(false, 'Selected date must be in the future');
}

// Format date for display
$formattedDate = $selectedDate->format('l, F j, Y');

// Generate unique booking ID
$bookingId = 'PYRA-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

// Prepare email content for admin
$to = "info@pyramedia.ae"; // Replace with your actual email
$subject = "New Consultation Booking - {$bookingId}";

$emailBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #FF6B35; }
        .value { margin-top: 5px; padding: 10px; background: white; border-left: 3px solid #FF6B35; }
        .booking-id { background: #FF6B35; color: white; padding: 10px 20px; border-radius: 5px; display: inline-block; font-weight: bold; margin: 20px 0; }
        .appointment-box { background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>📅 New Consultation Booking</h2>
        </div>
        <div class='content'>
            <div class='booking-id'>Booking ID: {$bookingId}</div>

            <div class='appointment-box'>
                <h3 style='margin: 0 0 10px 0;'>Scheduled Appointment</h3>
                <p style='font-size: 20px; margin: 5px 0;'><strong>{$formattedDate}</strong></p>
                <p style='font-size: 18px; margin: 5px 0;'><strong>{$appointmentTime}</strong></p>
            </div>

            <div class='field'>
                <div class='label'>Client Name:</div>
                <div class='value'>{$clientName}</div>
            </div>
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'>{$clientEmail}</div>
            </div>
            <div class='field'>
                <div class='label'>Phone:</div>
                <div class='value'>{$clientPhone}</div>
            </div>";

if (!empty($companyName)) {
    $emailBody .= "
            <div class='field'>
                <div class='label'>Company:</div>
                <div class='value'>{$companyName}</div>
            </div>";
}

$emailBody .= "
            <div class='field'>
                <div class='label'>How Can We Help:</div>
                <div class='value'>{$helpDescription}</div>
            </div>
            <div class='field'>
                <div class='label'>Booked On:</div>
                <div class='value'>" . date('F j, Y \a\t g:i A') . "</div>
            </div>
        </div>
    </div>
</body>
</html>
";

// Email headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: PYRAMEDIA Bookings <noreply@pyramedia.ae>" . "\r\n";
$headers .= "Reply-To: {$clientEmail}" . "\r\n";

// Send email to admin
$adminEmailSent = mail($to, $subject, $emailBody, $headers);

// Send confirmation email to client
$clientSubject = "Consultation Confirmed - {$bookingId}";
$clientEmailBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .appointment-box { background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 25px; border-radius: 10px; text-align: center; margin: 20px 0; }
        .info-box { background: white; padding: 20px; border-left: 4px solid #FF6B35; margin: 20px 0; }
        .button { display: inline-block; background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>✅ Consultation Confirmed!</h1>
        </div>
        <div class='content'>
            <p>Dear <strong>{$clientName}</strong>,</p>
            <p>Thank you for booking a free consultation with PYRAMEDIA! We're excited to help you transform your digital presence.</p>

            <div class='appointment-box'>
                <h2 style='margin: 0 0 15px 0;'>Your Appointment Details</h2>
                <p style='font-size: 22px; margin: 10px 0;'><strong>📅 {$formattedDate}</strong></p>
                <p style='font-size: 20px; margin: 10px 0;'><strong>🕒 {$appointmentTime}</strong></p>
                <p style='margin-top: 15px; font-size: 14px;'>Booking ID: <strong>{$bookingId}</strong></p>
            </div>

            <div class='info-box'>
                <h3 style='color: #FF6B35; margin-top: 0;'>What to Expect:</h3>
                <ul style='line-height: 2;'>
                    <li>One-on-one consultation with our expert team</li>
                    <li>Comprehensive analysis of your current digital presence</li>
                    <li>Tailored recommendations for your business</li>
                    <li>Q&A session to address all your concerns</li>
                </ul>
            </div>

            <div class='info-box'>
                <h3 style='color: #FF6B35; margin-top: 0;'>Before the Meeting:</h3>
                <ul style='line-height: 2;'>
                    <li>We'll send you a meeting link 24 hours before the appointment</li>
                    <li>Please prepare any questions you'd like to discuss</li>
                    <li>Have information about your current marketing efforts ready</li>
                </ul>
            </div>

            <div class='info-box'>
                <h3 style='color: #FF6B35; margin-top: 0;'>Need to Reschedule?</h3>
                <p>If you need to change your appointment, please contact us at:</p>
                <p><strong>Email:</strong> info@pyramedia.ae<br>
                <strong>Phone:</strong> +971 XX XXX XXXX</p>
                <p><em>Please include your Booking ID: {$bookingId}</em></p>
            </div>

            <div style='text-align: center; margin-top: 30px;'>
                <p>Add this appointment to your calendar:</p>
                <a href='#' class='button'>📅 Add to Calendar</a>
            </div>

            <p style='margin-top: 40px;'>We look forward to meeting you!</p>
            <p>Best regards,<br><strong>The PYRAMEDIA Team</strong></p>
        </div>
    </div>
</body>
</html>
";

$clientHeaders = "MIME-Version: 1.0" . "\r\n";
$clientHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$clientHeaders .= "From: PYRAMEDIA <info@pyramedia.ae>" . "\r\n";
$clientHeaders .= "Reply-To: info@pyramedia.ae" . "\r\n";

$clientEmailSent = mail($clientEmail, $clientSubject, $clientEmailBody, $clientHeaders);

// Generate ICS calendar file content
$icsContent = generateICS($bookingId, $clientName, $clientEmail, $appointmentDate, $appointmentTime, $helpDescription);

// Save ICS file (optional - create calendar directory first)
/*
$icsFilePath = "../calendar/{$bookingId}.ics";
file_put_contents($icsFilePath, $icsContent);
*/

// n8n Webhook Integration
$webhookUrl = "https://your-n8n-instance.com/webhook/pyramedia-booking"; // Replace with your n8n webhook URL

$webhookData = [
    'booking_id' => $bookingId,
    'client_name' => $clientName,
    'client_email' => $clientEmail,
    'client_phone' => $clientPhone,
    'company_name' => $companyName,
    'help_description' => $helpDescription,
    'appointment_date' => $appointmentDate,
    'appointment_time' => $appointmentTime,
    'formatted_date' => $formattedDate,
    'booked_at' => date('c'),
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

// Log booking (optional - create logs directory first)
/*
$logEntry = date('Y-m-d H:i:s') . " | {$bookingId} | {$clientName} | {$clientEmail} | {$formattedDate} at {$appointmentTime}\n";
file_put_contents('../logs/booking_log.txt', $logEntry, FILE_APPEND);
*/

if ($adminEmailSent && $clientEmailSent) {
    sendResponse(true, 'Booking confirmed! Check your email for details.', [
        'booking_id' => $bookingId,
        'appointment_date' => $formattedDate,
        'appointment_time' => $appointmentTime,
        'ics_content' => base64_encode($icsContent)
    ]);
} else {
    sendResponse(false, 'Failed to confirm booking. Please try again later.');
}

// Function to generate ICS calendar file
function generateICS($bookingId, $clientName, $clientEmail, $date, $time, $description) {
    // Convert date and time to ICS format
    $startDateTime = new DateTime($date . ' ' . $time);
    $endDateTime = clone $startDateTime;
    $endDateTime->modify('+1 hour'); // 1-hour consultation

    $icsDateStart = $startDateTime->format('Ymd\THis');
    $icsDateEnd = $endDateTime->format('Ymd\THis');
    $icsDateStamp = gmdate('Ymd\THis\Z');

    $icsContent = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//PYRAMEDIA//Consultation Booking//EN
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
UID:{$bookingId}@pyramedia.ae
DTSTAMP:{$icsDateStamp}
DTSTART:{$icsDateStart}
DTEND:{$icsDateEnd}
SUMMARY:PYRAMEDIA Consultation - {$clientName}
DESCRIPTION:{$description}
LOCATION:Online Meeting (Link will be sent 24 hours before)
STATUS:CONFIRMED
SEQUENCE:0
ORGANIZER;CN=PYRAMEDIA:mailto:info@pyramedia.ae
ATTENDEE;CN={$clientName};RSVP=TRUE:mailto:{$clientEmail}
BEGIN:VALARM
TRIGGER:-PT1H
ACTION:DISPLAY
DESCRIPTION:Reminder: PYRAMEDIA Consultation in 1 hour
END:VALARM
END:VEVENT
END:VCALENDAR";

    return $icsContent;
}
?>
