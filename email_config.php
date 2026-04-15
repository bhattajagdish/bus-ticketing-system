<?php
// Email Configuration for Ticket Cancellation Notifications
// Using PHPMailer with Gmail SMTP

// Detect PHPMailer path (project may have different folder names)
$possible_paths = [
    __DIR__ . '/PHPMailer/src/',
    __DIR__ . '/PHPMailer-7.0.2/src/',
    __DIR__ . '/PHPMailer-7.1.0/src/',
    __DIR__ . '/vendor/phpmailer/phpmailer/src/'
];

$phpmailer_path = null;
foreach ($possible_paths as $p) {
    if (file_exists($p . 'PHPMailer.php')) {
        $phpmailer_path = $p;
        break;
    }
}

if ($phpmailer_path === null) {
    error_log("PHPMailer not found! Expected in one of: " . implode(', ', $possible_paths));
    // Define a no-op function so callers won't fatally error when trying to send email
    function sendCancellationEmail($to, $data)
    {
        error_log("sendCancellationEmail called but PHPMailer is not installed. To enable email, install PHPMailer and/or adjust email_config.php paths.");
        return false;
    }
    return;
}

// SMTP/Gmail credentials (used throughout the app)
if (!defined('GMAIL_ADDRESS')) {
    define('GMAIL_ADDRESS', 'bhattajagdish606@gmail.com');
}
if (!defined('GMAIL_PASSWORD')) {
    define('GMAIL_PASSWORD', 'biqjosabojjozrlm');
}

// default sender name
if (!defined('SMTP_FROM_NAME')) {
    define('SMTP_FROM_NAME', 'Bus Ticketing System');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $phpmailer_path . 'Exception.php';
require $phpmailer_path . 'PHPMailer.php';
require $phpmailer_path . 'SMTP.php';

function sendCancellationEmail($to, $data)
{
    try {
        // Create the PHPMailer instance inside the try so construction errors are caught
        $mail = new PHPMailer(true);

        // SMTP Server Configuration (Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = GMAIL_ADDRESS;
        $mail->Password   = GMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Details
        $mail->setFrom(GMAIL_ADDRESS, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Ticket Cancellation Confirmation';

        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background-color: #ff6b6b; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .info { background-color: white; padding: 15px; margin: 10px 0; border-left: 4px solid #ff6b6b; }
                .footer { background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🚍 Ticket Cancelled Successfully</h2>
                </div>
                
                <div class='content'>
                    <p>Dear User,</p>
                    <p>Your ticket has been cancelled as requested. Below are the details:</p>
                    
                    <div class='info'>
                        <p><strong>Booking ID:</strong> " . htmlspecialchars($data['booking_id']) . "</p>
                        <p><strong>Bus Number:</strong> " . htmlspecialchars($data['bus_number']) . "</p>
                        <p><strong>Route:</strong> " . htmlspecialchars($data['route']) . "</p>
                        <p><strong>Seat Number:</strong> " . htmlspecialchars($data['seat_number']) . "</p>
                        <p><strong>Passengers:</strong> " . htmlspecialchars($data['passengers']) . "</p>
                        <p><strong>Total Amount:</strong> Rs. " . htmlspecialchars(number_format($data['total_amount'], 2)) . "</p>
                        <p><strong>Refund Amount:</strong> Rs. " . htmlspecialchars(number_format($data['refund_amount'], 2)) . "</p>
                        <p><strong>Trip Date:</strong> " . htmlspecialchars($data['trip_date']) . "</p>
                        <p><strong>Departure Time:</strong> " . htmlspecialchars($data['departure_time']) . "</p>
                        <p><strong>Cancellation Time:</strong> " . htmlspecialchars($data['cancel_time']) . "</p>
                    </div>
                    
                    <p style='color: #27ae60; font-weight: bold;'>✓ Your refund will be processed within 5-7 business days.</p>
                    
                    <hr>
                    <p><strong>Need Help?</strong></p>
                    <p>Contact us: support@bus-ticketing-system.com</p>
                    <p>Phone: +977-9744437623</p>
                </div>
                
                <div class='footer'>
                    <p>&copy; 2026 Bus Ticketing System. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        $msg = "Email exception: " . $e->getMessage();
        if (isset($mail) && property_exists($mail, 'ErrorInfo')) {
            $msg .= ' | PHPMailer ErrorInfo: ' . $mail->ErrorInfo;
        }
        error_log($msg);
        return false;
    }
}
?>