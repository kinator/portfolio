<?php
session_start();
session_destroy();
session_start(); // Start a new session to store the flash message
$_SESSION['mesgs']['confirm'][] = 'Déconnecté avec succés';
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
header("Location: " . $base_url . "/");
exit();
?>