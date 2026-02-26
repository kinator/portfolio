<?php
include $root . '/inc/head.php';
?>

<div class="w3-container w3-padding-32">
    <!-- Import GitHub -->
    <div class="w3-card w3-white w3-margin-bottom w3-round">
        <header class="w3-container w3-light-grey">
            <h3>Importer depuis GitHub</h3>
        </header>
        <div class="w3-container w3-padding">
            <form action="import_github" method="post" class="w3-container">
                <label>Nom d'utilisateur GitHub</label>
                <input class="w3-input w3-border w3-round" type="text" name="github_username" placeholder="Nom d'utilisateur GitHub" required>
                <button type="submit" class="w3-button w3-black w3-margin-top w3-round">Importer</button>
            </form>
        </div>
    </div>

    <!-- Projects List -->
    <div class="w3-card w3-white w3-round">
        <div class="w3-container w3-light-grey w3-padding">
            <h2 class="w3-left" style="margin:0">Gestion des Projets</h2>
            <a href="add_project.php" class="w3-button w3-blue w3-right w3-round">+ Nouveau Projet</a>
        </div>
        
        <div class="w3-responsive">
            <table class="w3-table-all w3-hoverable">
                <thead>
                    <tr class="w3-light-grey">
                        <th>Nom du Projet</th>
                        <th>Description</th>
                        <th>Compétences</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($projects) > 0): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?= htmlspecialchars($project['nom_proj']) ?></td>
                                <td><?= htmlspecialchars($project['commentaire_proj']) ?></td>
                                <td>
                                    <?php 
                                    $skills = trim($project['competences'] ?? '', '{}');
                                    if ($skills) {
                                        $skillsArray = explode(',', $skills);
                                        foreach ($skillsArray as $skill) {
                                            $skill = trim($skill, '"');
                                            echo '<span class="w3-tag w3-info w3-round w3-small" style="margin-right:5px;">' . htmlspecialchars($skill) . '</span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="edit_project.php?id=<?= $project['id_proj'] ?>" class="w3-button w3-tiny w3-amber w3-round">Éditer</a>
                                    <a href="delete_project?id=<?= $project['id_proj'] ?>" class="w3-button w3-tiny w3-red w3-round" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="w3-center">Aucun projet trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $root . '/inc/footer.php'; ?>