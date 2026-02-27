<?php
$pdo = require $root . '/lib/pdo.php';

$projects = [];

try {
    $query = "SELECT * FROM projects_view WHERE visible = True ORDER BY id_proj DESC";
    $stmt = $pdo->query($query);
    $dbProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dbProjects as $project) {
        // Fetch the first image for the project
        $stmtImg = $pdo->prepare("SELECT img_url FROM projets_images WHERE id_proj = ? LIMIT 1");
        $stmtImg->execute([$project['id_proj']]);
        $imgUrl = $stmtImg->fetchColumn();

        $tags = [];
        $skills = trim($project['competences'] ?? '', '{}');
        if ($skills) {
            $skillsArray = explode(',', $skills);
            foreach ($skillsArray as $skill) {
                $tags[] = trim($skill, '"');
            }
        }

        $projects[] = [
            'title' => $project['nom_proj'],
            'description' => $project['desc_proj'],
            'image' => $imgUrl ? $base_url . '/' . $imgUrl : 'https://via.placeholder.com/400x300?text=' . urlencode($project['nom_proj']),
            'link' => $project['lien_proj'],
            'tags' => $tags
        ];
    }
} catch (Exception $e) {
    if (isset($_SESSION['login']) && isset($_SESSION['auth_token'])) {
        $_SESSION['mesgs']['errors'][] = "Erreur de récupération des projets : " . $e->getMessage();
    }
}

$pageTitle = 'Mes Projets';
require_once $root . '/views/projets/index.view.php';