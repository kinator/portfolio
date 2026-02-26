<?php
$messageSent = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real application, you would send an email here.
    // mail($to, $subject, $message, $headers);
    $messageSent = true;
}

$pageTitle = 'Contact';
require_once $root . '/views/contact/index.view.php';
