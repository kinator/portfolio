<?php
$projects = [
    [
        'title' => 'Portfolio',
        'description' => 'Le site sur lequel vous naviguez actuellement. Créé from scratch en PHP.',
        'image' => 'https://via.placeholder.com/400x300?text=Portfolio',
        'link' => '#',
        'tags' => ['PHP', 'MVC', 'W3.CSS']
    ],
    [
        'title' => 'E-Commerce',
        'description' => 'Une boutique en ligne complète avec gestion de panier et paiement.',
        'image' => 'https://via.placeholder.com/400x300?text=E-Commerce',
        'link' => '#',
        'tags' => ['PHP', 'MySQL', 'Stripe']
    ],
    [
        'title' => 'Blog Tech',
        'description' => 'Un blog multi-utilisateurs avec système de commentaires et modération.',
        'image' => 'https://via.placeholder.com/400x300?text=Blog',
        'link' => '#',
        'tags' => ['Laravel', 'Vue.js']
    ]
];

$pageTitle = 'Mes Projets';
require_once $root . '/views/projets/index.view.php';