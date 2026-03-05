<?php
$pdo = require $root . '/lib/pdo.php';

$projects = [];

try {
    $query = "SELECT id_proj, nom_proj, desc_proj, commentaire_proj, lien_proj, images, competences FROM projects_view WHERE visible = True ORDER BY id_proj DESC";
    $stmt = $pdo->query($query);
    $dbProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dbProjects as $project) {
        $images = $project['images'] ? explode(',', trim($project['images'], "{}")) : [];
        $baseImgUrl = $images[0] ?? null;

        $tags = [];
        $skills = $project['competences'] ? trim($project['competences'], '{}') : '';
        if ($skills) {
            $skillsArray = explode(',', $skills);
            foreach ($skillsArray as $skill) {
                $tags[] = trim($skill, '"');
            }
        }

        $projects[] = [
            'id' => $project['id_proj'],
            'title' => $project['nom_proj'],
            'description' => $project['desc_proj'],
            'commentary' => $project['commentaire_proj'] ?? '',
            'image' => $baseImgUrl ? $base_url . '/' . trim($baseImgUrl, '"') : $base_url . "/assets/img/kinator.jpg",
            'images' => array_map(function($img) use ($base_url) { return $base_url . '/' . trim($img, '"'); }, array_filter($images)),
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