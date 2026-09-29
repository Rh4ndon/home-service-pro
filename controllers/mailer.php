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
