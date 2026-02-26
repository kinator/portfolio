<?php
if (!isset($_SESSION['login']) || !isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $pdo = require $root . '/lib/pdo.php';

    try {
        $pdo->beginTransaction();

        // Delete associated competencies first (projets_competences)
        $stmtComp = $pdo->prepare("DELETE FROM projets_competences WHERE id_proj = ?");
        $stmtComp->execute([$id]);

        // Delete the project
        $stmtProj = $pdo->prepare("DELETE FROM projets WHERE id_proj = ?");
        $stmtProj->execute([$id]);

        $pdo->commit();
        $_SESSION['mesgs']['confirm'][] = "Projet supprimé avec succès.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['mesgs']['errors'][] = "Erreur lors de la suppression du projet : " . $e->getMessage();
    }
}

header('Location: dashboard');
exit();