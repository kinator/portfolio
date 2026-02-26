<?php
// Ensure user is logged in
if (!isset($_SESSION['login']) || !isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

// Connect to DB
$pdo = require $root . '/lib/pdo.php';

$projects = [];

if ($pdo) {
    try {
        $query = "SELECT * FROM projects_view ORDER BY id_proj DESC";
        $stmt = $pdo->query($query);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // In a real app, you might want to log this error instead of dying
        die("Erreur de récupération des projets : " . $e->getMessage());
    }
}

// Load view
require_once $root . '/views/dashboard/index.view.php';