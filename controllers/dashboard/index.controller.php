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
                    $stmtImg = $pdo->prepare("SELECT * FROM images WHERE id_proj = ?");
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
            $visibilite = isset($_POST['visibilite_proj']) ? true : false;
            if ($_POST['action'] === 'add_project') {
                $stmt = $pdo->prepare("INSERT INTO projets (nom_proj, desc_proj, commentaire_proj, lien_proj, visible) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['nom_proj'], $_POST['desc_proj'], $_POST['commentaire_proj'], $_POST['lien_proj'], $visibilite]);
                $projectId = $pdo->lastInsertId();
                $_SESSION['mesgs']['confirm'][] = "Projet ajouté avec succès.";
            } else { // update_project
                $projectId = $_POST['id_proj'];
                $stmt = $pdo->prepare("UPDATE projets SET nom_proj = ?, desc_proj = ?, commentaire_proj = ?, lien_proj = ?, visible = ? WHERE id_proj = ?");
                $stmt->execute([$_POST['nom_proj'], $_POST['desc_proj'], $_POST['commentaire_proj'], $_POST['lien_proj'], $visibilite, $projectId]);

                if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                    $delStmt = $pdo->prepare("DELETE FROM images WHERE id_img = ?");
                    $pathStmt = $pdo->prepare("SELECT url_img FROM images WHERE id_img = ?");
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
                if (!is_dir($targetDir)) {
                    if (!mkdir($targetDir, 0777, true)) {
                        throw new Exception("Impossible de créer le dossier d'images.");
                    }
                }
                if (!is_writable($targetDir)) throw new Exception("Le dossier d'images n'est pas accessible en écriture.");
                $insStmt = $pdo->prepare("INSERT INTO images (id_proj, url_img) VALUES (?, ?)");
                foreach ($_FILES['new_images']['tmp_name'] as $k => $tmp) {
                    $error = $_FILES['new_images']['error'][$k];
                    if ($error === UPLOAD_ERR_OK) {
                        if (is_uploaded_file($tmp)) {
                            $info = pathinfo($_FILES['new_images']['name'][$k]);
                            $ext = $info['extension'];
                            $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $info['filename']);
                            $fname = uniqid('proj_') . '_' . $name . '.' . $ext;
                            if (move_uploaded_file($tmp, $targetDir . $fname)) {
                                $insStmt->execute([$projectId, 'img/projects/' . $fname]);
                            } else {
                                $err = error_get_last();
                                $_SESSION['mesgs']['errors'][] = "Erreur lors de l'enregistrement de l'image " . $_FILES['new_images']['name'][$k] . " : " . ($err['message'] ?? 'Raison inconnue');
                            }
                        }
                    } elseif ($error !== UPLOAD_ERR_NO_FILE) {
                        $fileName = $_FILES['new_images']['name'][$k];
                        $msg = "Erreur upload ($fileName): ";
                        switch ($error) {
                            case UPLOAD_ERR_INI_SIZE: $msg .= "Fichier trop lourd (server limit)."; break;
                            case UPLOAD_ERR_FORM_SIZE: $msg .= "Fichier trop lourd (form limit)."; break;
                            case UPLOAD_ERR_PARTIAL: $msg .= "Upload partiel."; break;
                            case UPLOAD_ERR_NO_TMP_DIR: $msg .= "Dossier temporaire manquant."; break;
                            case UPLOAD_ERR_CANT_WRITE: $msg .= "Échec écriture disque."; break;
                            default: $msg .= "Code erreur $error.";
                        }
                        $_SESSION['mesgs']['errors'][] = $msg;
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