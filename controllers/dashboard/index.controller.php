<?php
// Ensure user is logged in
if (!isset($_SESSION['login']) || !isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

// Connect to DB
$pdo = require $root . '/lib/pdo.php';

/**
 * Helper function to fetch data from GitHub API.
 *
 * @param string $url The API URL to fetch.
 * @return mixed|null Decoded JSON response or null on failure.
 */
function fetchGithub($url) {
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Portfolio-Importer'
            ]
        ]
    ];
    $context = stream_context_create($opts);
    $data = @file_get_contents($url, false, $context);
    return $data ? json_decode($data, true) : null;
}

/**
 * Handles all POST requests (Add, Update, Import) and AJAX GET requests (Fetch project data).
 *
 * @param PDO $pdo Database connection object.
 * @param string $root Root directory path for file operations.
 */
function handleEditRequest($pdo, $root) {
    // Handle AJAX request to retrieve project details for the edit modal
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

    // Handle project deletion request
    if (isset($_GET['action']) && $_GET['action'] === 'delete_project' && isset($_GET['id'])) {
        $id = $_GET['id'];
        try {
            $pdo->beginTransaction();

            // Delete associated competencies first (projets_competences)
            $stmtComp = $pdo->prepare("DELETE FROM projets_competences WHERE id_proj = ?");
            $stmtComp->execute([$id]);

            // Delete associated images
            $stmtImg = $pdo->prepare("SELECT url_img FROM images WHERE id_proj = ?");
            $stmtImg->execute([$id]);
            $images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
            foreach ($images as $img) {
                if ($img && file_exists($root . '/' . $img)) {
                    unlink($root . '/' . $img);
                }
            }
            $stmtDelImg = $pdo->prepare("DELETE FROM images WHERE id_proj = ?");
            $stmtDelImg->execute([$id]);

            // Delete the project
            $stmtProj = $pdo->prepare("DELETE FROM projets WHERE id_proj = ?");
            $stmtProj->execute([$id]);

            $pdo->commit();
            $_SESSION['mesgs']['confirm'][] = "Projet supprimé avec succès.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['mesgs']['errors'][] = "Erreur lors de la suppression du projet : " . $e->getMessage();
        }
        header('Location: dashboard');
        exit;
    }

    // Handle POST form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add_project', 'update_project', 'import_github'])) {
        try {
            // Action: Import projects from GitHub
            if ($_POST['action'] === 'import_github') {
                if (!empty($_POST['github_username'])) {
                    $username = $_POST['github_username'];
                    
                    // Fetch repos (limit to 1000)
                    $repos = fetchGithub("https://api.github.com/users/" . urlencode($username) . "/repos?per_page=1000");

                    if ($repos && is_array($repos)) {
                        $count = 0;
                        
                        // Prepare statements
                        $stmtCheck = $pdo->prepare("SELECT id_proj FROM projets WHERE nom_proj = ?");
                        $stmtInsert = $pdo->prepare("INSERT INTO projets (nom_proj, desc_proj, commentaire_proj, lien_proj) VALUES (?, ?, ?, ?)");
                        $stmtLink = $pdo->prepare("INSERT INTO projets_competences (id_proj, id_comp) VALUES (?, ?) ON CONFLICT DO NOTHING");

                        // Load competencies map to match GitHub languages to DB IDs
                        $compStmt = $pdo->query("SELECT id_comp, name FROM competences");
                        $allComps = $compStmt->fetchAll(PDO::FETCH_ASSOC);
                        $compMap = [];
                        foreach($allComps as $c) {
                            $compMap[strtoupper($c['name'])] = $c['id_comp'];
                            $compMap[strtoupper($c['id_comp'])] = $c['id_comp'];
                        }
                        
                        // Add aliases for common GitHub language names to DB IDs
                        $aliases = [
                            'VUE' => 'VUEJS',
                            'DOCKERFILE' => 'DOCKER',
                            'SHELL' => 'LINUX',
                            'JUPYTER NOTEBOOK' => 'PYTHON'
                        ];
                        foreach ($aliases as $alias => $id) {
                            // Only add alias if the target ID exists in the database
                            if (in_array($id, $compMap)) $compMap[$alias] = $id;
                        }

                        foreach ($repos as $repo) {
                            // Skip forks
                            if (isset($repo['fork']) && $repo['fork'] && isset($repo['parent'])) continue;

                            $name = $repo['name'];
                            $desc = $repo['description'] ?? '';
                            $link = $repo['html_url'] ?? '';
                            
                            // Check if project exists
                            $stmtCheck->execute([$name]);
                            $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                            
                            $projId = null;
                            if (!$existing) {
                                $stmtInsert->execute([$name, $desc, '', $link]);
                                $projId = $pdo->lastInsertId();
                                $count++;
                            } else {
                                continue;
                            }

                            // Fetch and link languages
                            if (isset($repo['languages_url'])) {
                                $langs = fetchGithub($repo['languages_url']);
                                if ($langs && is_array($langs)) {
                                    foreach ($langs as $langName => $bytes) {
                                        $upperLang = strtoupper($langName);
                                        if (isset($compMap[$upperLang])) {
                                            $stmtLink->execute([$projId, $compMap[$upperLang]]);
                                        }
                                    }
                                }
                            }

                            // Check topics (keywords)
                            if (isset($repo['topics']) && is_array($repo['topics'])) {
                                foreach ($repo['topics'] as $topic) {
                                    $upperTopic = strtoupper($topic);
                                    if (isset($compMap[$upperTopic])) {
                                        $stmtLink->execute([$projId, $compMap[$upperTopic]]);
                                    }
                                }
                            }

                            // Check description for keywords
                            if (!empty($desc)) {
                                $upperDesc = strtoupper($desc);
                                foreach ($compMap as $keyword => $idComp) {
                                    if (preg_match('/(^|[^A-Z0-9])' . preg_quote($keyword, '/') . '([^A-Z0-9]|$)/', $upperDesc)) {
                                        $stmtLink->execute([$projId, $idComp]);
                                    }
                                }
                            }
                        }
                        $_SESSION['mesgs']['confirm'][] = "$count nouveaux projets importés depuis GitHub.";
                    } else {
                        $_SESSION['mesgs']['errors'][] = "Impossible de récupérer les dépôts GitHub (Utilisateur introuvable ou erreur API).";
                    }
                }
            } else {
                // Action: Add or Update a project manually
                $visibilite = isset($_POST['visibilite_proj']) ? 1 : 0;
                if ($_POST['action'] === 'add_project') {
                    $stmt = $pdo->prepare("INSERT INTO projets (nom_proj, desc_proj, commentaire_proj, lien_proj, visible) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$_POST['nom_proj'], $_POST['desc_proj'], $_POST['commentaire_proj'], $_POST['lien_proj'], $visibilite]);
                    $projectId = $pdo->lastInsertId();
                    $_SESSION['mesgs']['confirm'][] = "Projet ajouté avec succès.";
                } else { // update_project
                    $projectId = $_POST['id_proj'];
                    $stmt = $pdo->prepare("UPDATE projets SET nom_proj = ?, desc_proj = ?, commentaire_proj = ?, lien_proj = ?, visible = ? WHERE id_proj = ?");
                    $stmt->execute([$_POST['nom_proj'], $_POST['desc_proj'], $_POST['commentaire_proj'], $_POST['lien_proj'], $visibilite, $projectId]);

                    // Handle image deletion if requested
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

                // Handle new image uploads
                if (isset($_FILES['new_images'])) {
                    $targetDir = $root . '/assets/img/projects/';
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
                                    $insStmt->execute([$projectId, 'assets/img/projects/' . $fname]);
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
            }
        } catch (Exception $e) {
            $_SESSION['mesgs']['errors'][] = "$visibilite, Erreur à la ligne " . $e->getLine() . ": " . $e->getMessage();
        }
        header('Location: dashboard');
        exit;
    }
}

// Execute request handler if DB connection is active
if ($pdo) {
    handleEditRequest($pdo, $root);
}

$projects = [];

// Fetch all projects for display in the dashboard list
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