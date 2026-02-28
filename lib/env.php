<?php

try {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 2));
    $dotenv->load();

    define("CONTACT_EMAIL", $_ENV['CONTACT_EMAIL'] ?? null);
    define("SMTP_HOST", $_ENV['SMTP_HOST'] ?? null);
    define("SMTP_USER", $_ENV['SMTP_USER'] ?? null);
    define("SMTP_PASS", $_ENV['SMTP_PASS'] ?? null);
    define("SMTP_PORT", $_ENV['SMTP_PORT'] ?? 587);
    define("SMTP_SECURE", $_ENV['SMTP_SECURE'] ?? 'tls');
} catch (Exception $e) {
    // Handle the error gracefully
    error_log("Error loading environment variables: " . $e->getMessage());
}
