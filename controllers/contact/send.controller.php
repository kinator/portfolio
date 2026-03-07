<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

ob_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Honeypot check: if filled, it's a bot. Pretend success.
        if (!empty($_POST['website_hp'])) {
            echo json_encode(['success' => true]);
            exit;
        }

        // 1. CSRF Security Check
        if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => "Erreur de sécurité (Session invalide). Veuillez rafraîchir la page."]);
            exit;
        }

        // 2. Rate Limiting (Prevent spam flooding: 1 email per 60 seconds)
        if (isset($_SESSION['last_contact_time']) && (time() - $_SESSION['last_contact_time'] < 60)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => "Veuillez patienter une minute avant de renvoyer un message."]);
            exit;
        }
        $_SESSION['last_contact_time'] = time();

        $to = CONTACT_EMAIL;
        // 3. Input Sanitization
        $subject = strip_tags(trim($_POST['Subject'] ?? 'No Subject'));
        $message = "Name: " . strip_tags(trim($_POST['Name'] ?? 'N/A')) . "\n";
        $message .= "Email: " . strip_tags(trim($_POST['Email'] ?? 'N/A')) . "\n\n";
        $message .= "Phone: " . strip_tags(trim($_POST['Phone'] ?? 'N/A')) . "\n\n";
        $message .= "At: " . date('Y-m-d H:i:s') . "\n\n";
        $message .= strip_tags(trim($_POST['Message'] ?? 'No message provided'));

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
        $replyEmail = trim($_POST['Email'] ?? '');
        $replyName = strip_tags(trim($_POST['Name'] ?? ''));
        if (filter_var($replyEmail, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyEmail, $replyName);
        }

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        ob_clean();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log("Error preparing email: " . $e->getMessage());
        ob_clean();
        echo json_encode(['success' => false, 'message' => "Une erreur technique est survenue."]);
    }
} else {
    http_response_code(405);
    ob_clean();
    echo json_encode(['success' => false, 'message' => "Method Not Allowed"]);
}