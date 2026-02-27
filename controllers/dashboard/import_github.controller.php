<?php
if (!isset($_SESSION['login']) || !isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['github_username'])) {
    $username = $_POST['github_username'];
    $pdo = require $root . '/lib/pdo.php';

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

    // Fetch repos (limit to 100)
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
            if (isset($repo['fork']) && $repo['fork']) continue;

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

header('Location: dashboard');
exit();