<?php
// Ensure user is logged in
if (!isset($_SESSION['login']) || !isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

// Connect to DB
$pdo = require $root . '/lib/pdo.php';

function handleEditRequest($pdo, $root) {
    if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_project' && isset($_GET['id'])) {
        header('Content-Type: application/json');
        try {
            $stmt = $pdo->prepare("SELECT * FROM projets WHERE id_proj = ?");
            $stmt->execute([$_GET['id']]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($project) {
                try {
                    $stmtImg = $pdo->prepare("SELECT * FROM projets_images WHERE id_proj = ?");
                    $stmtImg->execute([$_GET['id']]);
                    $project['images'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) { $project['images'] = []; }
            }
            echo json_encode($project);
        } catch (Exception $e) { echo json_encode(['error' => $e->getMessage()]); }
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add_project', 'update_project'])) {
        try {
            if ($_POST['action'] === 'add_project') {
                $stmt = $pdo->prepare("INSERT INTO projets (nom_proj, desc_proj, commentaire_proj, lien_proj) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['nom_proj'], $_POST['desc_proj'], $_POST['commentaire_proj'], $_POST['lien_proj']]);
                $projectId = $pdo->lastInsertId();
                $_SESSION['mesgs']['confirm'][] = "Projet ajouté avec succès.";
            } else { // update_project
                $projectId = $_POST['id_proj'];
                $stmt = $pdo->prepare("UPDATE projets SET nom_proj = ?, desc_proj = ?, commentaire_proj = ?, lien_proj = ? WHERE id_proj = ?");
                $stmt->execute([$_POST['nom_proj'], $_POST['desc_proj'], $_POST['commentaire_proj'], $_POST['lien_proj'], $projectId]);

                if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                    $delStmt = $pdo->prepare("DELETE FROM projets_images WHERE id_img = ?");
                    $pathStmt = $pdo->prepare("SELECT img_url FROM projets_images WHERE id_img = ?");
                    foreach ($_POST['delete_images'] as $imgId) {
                        $pathStmt->execute([$imgId]);
                        $path = $pathStmt->fetchColumn();
                        if ($path && file_exists($root . '/' . $path)) unlink($root . '/' . $path);
                        $delStmt->execute([$imgId]);
                    }
                }
                $_SESSION['mesgs']['confirm'][] = "Projet mis à jour.";
            }

            // Common image upload logic
            if (isset($_FILES['new_images'])) {
                $targetDir = $root . '/img/projects/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
                $insStmt = $pdo->prepare("INSERT INTO projets_images (id_proj, img_url) VALUES (?, ?)");
                foreach ($_FILES['new_images']['tmp_name'] as $k => $tmp) {
                    if (is_uploaded_file($tmp) && $_FILES['new_images']['error'][$k] === UPLOAD_ERR_OK) {
                        $ext = pathinfo($_FILES['new_images']['name'][$k], PATHINFO_EXTENSION);
                        $fname = uniqid('proj_') . '.' . $ext;
                        if (move_uploaded_file($tmp, $targetDir . $fname)) {
                            $insStmt->execute([$projectId, 'img/projects/' . $fname]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $_SESSION['mesgs']['errors'][] = "Erreur: " . $e->getMessage();
        }
        header('Location: dashboard');
        exit;
    }
}

if ($pdo) {
    handleEditRequest($pdo, $root);
}

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