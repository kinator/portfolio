<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$messageSent = false;
$errorMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Honeypot check: if filled, it's a bot. Pretend success.
        if (!empty($_POST['website_hp'])) {
            $messageSent = true;
        } else {
            $to = CONTACT_EMAIL;
            $subject = $_POST['Subject'] ?? 'No Subject';
            $message = "Name: " . ($_POST['Name'] ?? 'N/A') . "\n";
            $message .= "Email: " . ($_POST['Email'] ?? 'N/A') . "\n\n";
            $message .= "Phone: " . ($_POST['Phone'] ?? 'N/A') . "\n\n";
            $message .= "At: " . date('Y-m-d H:i:s') . "\n\n";
            $message .= $_POST['Message'] ?? 'No message provided';

            // Use PHPMailer with SMTP
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port       = SMTP_PORT;

            // Recipients
            $mail->setFrom(SMTP_USER, 'Portfolio Contact');
            $mail->addAddress(CONTACT_EMAIL);
            $mail->addReplyTo($_POST['Email'] ?? '', $_POST['Name'] ?? '');

            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            $messageSent = true;
        }
    } catch (Exception $e) {
        error_log("Error preparing email: " . $e->getMessage());
        $messageSent = false;
        $errorMessage = "Une erreur technique est survenue.";
    }
}

$pageTitle = 'Contact';
require_once $root . '/views/contact/index.view.php';
