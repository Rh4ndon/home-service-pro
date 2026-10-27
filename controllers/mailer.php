<?php
// Import PHPMailer classes

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function smtpEnv($key, $default = '')
{
    if (function_exists('envValue')) {
        $value = envValue($key);
        if ($value !== false && $value !== null && $value !== '') return $value;
    }
    $value = getenv($key);
    return ($value !== false && $value !== '') ? $value : $default;
}

/**
 * Send an HTML email. Returns true on success, false on failure.
 * Never writes to the output buffer: callers return JSON, and any stray
 * echo would corrupt the response body.
 */
function send_email($email, $name, $message, $alt_message, $subject)
{
    $host = smtpEnv('SMTP_HOST', 'smtp.gmail.com');
    $port = (int)smtpEnv('SMTP_PORT', '587');
    $secure = strtolower(smtpEnv('SMTP_SECURE', 'tls'));
    $user = smtpEnv('SMTP_USER');
    $pass = smtpEnv('SMTP_PASS');
    $fromAddress = smtpEnv('MAIL_FROM_ADDRESS', $user);
    $fromName = smtpEnv('MAIL_FROM_NAME', 'Home Service Pro');

    if (!$user || !$pass) {
        error_log('send_email: SMTP_USER/SMTP_PASS are not configured in .env');
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();                                      // Send using SMTP
        $mail->Host       = $host;                           // SMTP server
        $mail->SMTPAuth   = true;                             // Enable SMTP authentication
        $mail->Username   = $user;
        $mail->Password   = $pass;
        if ($secure === 'ssl' || $port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port       = $port;

        // Recipients
        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($email, $name);
        $mail->addReplyTo($fromAddress, $fromName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = $alt_message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('send_email failed: ' . $e->getMessage());
        return false;
    }
}

function generateOtp($length = 6)
{
    return str_pad((string)random_int(0, (int)str_repeat('9', $length) - 1), $length, '0', STR_PAD_LEFT);
}

function send_otp_email($email, $name, $otp, $context = 'login')
{
    $isWelcome = ($context === 'register');
    $greeting = $isWelcome
        ? 'Welcome to Home Service Pro, ' . $name
        : 'Good day, ' . $name;
    $intro = $isWelcome
        ? 'Confirm your email address to finish creating your account.'
        : 'Use this code to finish signing in. If you did not try to sign in, you can ignore this email.';

    $subject = 'Home Service Pro - Your Verification Code';
    $message = '
        <h3>' . htmlspecialchars($greeting, ENT_QUOTES, 'UTF-8') . '</h3>
        <p>' . htmlspecialchars($intro, ENT_QUOTES, 'UTF-8') . '</p>
        <p>Your verification code is:</p>
        <p style="font-size:28px;font-weight:bold;letter-spacing:4px;">' . htmlspecialchars($otp, ENT_QUOTES, 'UTF-8') . '</p>
        <p>This code expires shortly. Please do not share it with anyone.</p>
        <p>Home Service Pro</p>
        ';
    $alt_message = $greeting . "\n\n"
        . $intro . "\n\n"
        . 'Your verification code is: ' . $otp . "\n\n"
        . "This code expires shortly. Please do not share it with anyone.\n\n"
        . 'Home Service Pro';

    return send_email($email, $name, $message, $alt_message, $subject);
}

function send_new_ticket_notification($repairman_email, $repairman_name, $ticket_id, $client_name, $appliance_name, $appliance_type, $issue_desc, $preferred_date, $preferred_time, $type_of_service, $schedule_id, $client_address = '')
{
    $subject = 'Home Service Pro - New Ticket Assigned #' . $ticket_id;
    $greeting = 'Good day, ' . htmlspecialchars($repairman_name, ENT_QUOTES, 'UTF-8');
    $intro = 'A new service ticket has been assigned to you. Please review the details below:';
    
    $type_badge = ($type_of_service === 'Maintenance') 
        ? '<span style="background:#3b82f6;color:#fff;padding:4px 8px;border-radius:4px;font-size:12px;">Maintenance</span>'
        : '<span style="background:#f59e0b;color:#fff;padding:4px 8px;border-radius:4px;font-size:12px;">Repair</span>';

    // The appliance name already contains the type when it is built from make + type.
    $appliance_display = $appliance_name;
    if ($appliance_type !== '' && stripos($appliance_name, $appliance_type) === false) {
        $appliance_display .= ' (' . $appliance_type . ')';
    }

    $message = '
        <h3>' . $greeting . '</h3>
        <p>' . htmlspecialchars($intro, ENT_QUOTES, 'UTF-8') . '</p>
        <table style="border-collapse:collapse;width:100%;max-width:600px;margin:20px 0;font-family:Arial,sans-serif;">
            <tr><td style="padding:8px;border:1px solid #ddd;font-weight:bold;width:30%;">Ticket ID</td><td style="padding:8px;border:1px solid #ddd;">#' . $ticket_id . '</td></tr>
            <tr><td style="padding:8px;border:1px solid #ddd;font-weight:bold;">Type of Service</td><td style="padding:8px;border:1px solid #ddd;">' . $type_badge . '</td></tr>
            <tr><td style="padding:8px;border:1px solid #ddd;font-weight:bold;">Client</td><td style="padding:8px;border:1px solid #ddd;">' . htmlspecialchars($client_name, ENT_QUOTES, 'UTF-8') . '</td></tr>
            <tr><td style="padding:8px;border:1px solid #ddd;font-weight:bold;">Client Address</td><td style="padding:8px;border:1px solid #ddd;">' . htmlspecialchars($client_address, ENT_QUOTES, 'UTF-8') . '</td></tr>
            <tr><td style="padding:8px;border:1px solid #ddd;font-weight:bold;">Appliance</td><td style="padding:8px;border:1px solid #ddd;">' . htmlspecialchars($appliance_display, ENT_QUOTES, 'UTF-8') . '</td></tr>
            <tr><td style="padding:8px;border:1px solid #ddd;font-weight:bold;">Issue</td><td style="padding:8px;border:1px solid #ddd;">' . htmlspecialchars($issue_desc, ENT_QUOTES, 'UTF-8') . '</td></tr>
            <tr><td style="padding:8px;border:1px solid #ddd;font-weight:bold;">Preferred Date</td><td style="padding:8px;border:1px solid #ddd;">' . $preferred_date . '</td></tr>
            <tr><td style="padding:8px;border:1px solid #ddd;font-weight:bold;">Preferred Time</td><td style="padding:8px;border:1px solid #ddd;">' . $preferred_time . '</td></tr>
        </table>
        <p>Please log in to your dashboard to accept or manage this ticket.</p>
        <p style="margin-top:30px;">Home Service Pro</p>
        ';
    
    $alt_message = $greeting . "\n\n"
        . $intro . "\n\n"
        . "Ticket ID: #$ticket_id\n"
        . "Type of Service: $type_of_service\n"
        . "Client: $client_name\n"
        . "Client Address: $client_address\n"
        . "Appliance: $appliance_display\n"
        . "Issue: $issue_desc\n"
        . "Preferred Date: $preferred_date\n"
        . "Preferred Time: $preferred_time\n\n"
        . "Please log in to your dashboard to accept or manage this ticket.\n\n"
        . "Home Service Pro";

    return send_email($repairman_email, $repairman_name, $message, $alt_message, $subject);
}
