<?php
include $root . '/inc/head.php';
?>

<div class="w3-container w3-padding-32">
    <!-- Import GitHub Section -->
    <div class="w3-card w3-white w3-margin-bottom w3-round">
        <header class="w3-container w3-light-grey">
            <h3>Importer depuis GitHub</h3>
        </header>
        <div class="w3-container w3-padding">
            <form action="dashboard" method="post" class="w3-container">
                <input type="hidden" name="action" value="import_github">
                <label>Nom d'utilisateur GitHub</label>
                <input class="w3-input w3-border w3-round" type="text" name="github_username" placeholder="Nom d'utilisateur GitHub" required>
                <button type="submit" class="w3-button w3-black w3-margin-top w3-round">Importer</button>
            </form>
        </div>
    </div>

    <!-- Projects List Section -->
    <div class="w3-card w3-white w3-round">
        <div class="w3-container w3-light-grey w3-padding">
            <h2 class="w3-left" style="margin:0">Gestion des Projets</h2>
            <button onclick="openModal()" class="w3-button w3-blue w3-right w3-round">+ Nouveau Projet</button>
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
                                <td><?= htmlspecialchars($project['desc_proj']) ?></td>
                                <td>
                                    <!-- Display skills as tags -->
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
                                    <button onclick="openModal(<?= $project['id_proj'] ?>)" class="w3-button w3-tiny w3-amber w3-round">Éditer</button>
                                    <a href="dashboard?action=delete_project&id=<?= $project['id_proj'] ?>" class="w3-button w3-tiny w3-red w3-round" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">Supprimer</a>
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

<!-- Edit/Add Project Modal -->
<div id="editModal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-round" style="max-width:600px">
        <header class="w3-container w3-blue-grey w3-round-top"> 
            <span onclick="document.getElementById('editModal').style.display='none'" class="w3-button w3-display-topright w3-round">&times;</span>
            <h3 id="modal_title">Éditer le projet</h3>
        </header>
        <form id="edit_form" method="post" enctype="multipart/form-data" class="w3-container w3-padding">
            <input type="hidden" name="action" value="update_project">
            <input type="hidden" name="id_proj" id="edit_id_proj">
            
            <label>Nom du Projet</label>
            <input class="w3-input w3-border w3-round" type="text" name="nom_proj" id="edit_nom_proj" required>
            
            <label>Description</label>
            <textarea class="w3-input w3-border w3-round" name="desc_proj" id="edit_desc_proj" rows="3" style="resize:vertical"></textarea>

            <label>Commentaire</label>
            <textarea class="w3-input w3-border w3-round" name="commentaire_proj" id="edit_commentaire_proj" rows="3" style="resize:vertical"></textarea>
            
            <label>Lien</label>
            <input class="w3-input w3-border w3-round" type="text" name="lien_proj" id="edit_lien_proj">
            
            <p>
                <input class="w3-check" type="checkbox" name="visibilite_proj" id="edit_visibilite_proj" value="1">
                <label>Visible</label>
            </p>

            <div id="current_images_container">
                <label>Images actuelles</label>
                <div id="current_images" class="w3-row-padding w3-margin-bottom w3-border w3-round w3-padding"></div>
            </div>
            
            <label>Ajouter des images</label>
            <input class="w3-input w3-border w3-round" type="file" name="new_images[]" multiple accept="image/*">
            
            <button type="submit" id="modal_submit_button" class="w3-button w3-green w3-margin-top w3-round w3-right">Sauvegarder</button>
        </form>
        <div class="w3-container w3-padding-16"></div>
    </div>
</div>

<script>
/**
 * Opens the modal for adding or editing a project.
 * If an ID is provided, it fetches project data via AJAX and populates the form.
 * 
 * @param {number|null} id Project ID to edit, or null to add new
 */
function openModal(id = null) {
    document.getElementById('editModal').style.display='block';
    const form = document.getElementById('edit_form');
    form.reset();

    const modalTitle = document.getElementById('modal_title');
    const submitButton = document.getElementById('modal_submit_button');
    const currentImagesContainer = document.getElementById('current_images_container');
    const currentImagesDiv = document.getElementById('current_images');
    const actionInput = form.querySelector('input[name="action"]');
    const idInput = document.getElementById('edit_id_proj');

    if (id) { // Edit mode
        modalTitle.textContent = 'Éditer le projet';
        submitButton.textContent = 'Sauvegarder';
        actionInput.value = 'update_project';
        currentImagesContainer.style.display = 'block';
        currentImagesDiv.innerHTML = '<p>Chargement...</p>';
        
        fetch('<?= $base_url ?>/dashboard?ajax=get_project&id=' + id)
        .then(res => res.json())
        .then(data => {
            if(data.error) { alert(data.error); return; }
            idInput.value = data.id_proj;
            document.getElementById('edit_nom_proj').value = data.nom_proj;
            document.getElementById('edit_desc_proj').value = data.desc_proj;
            document.getElementById('edit_commentaire_proj').value = data.commentaire_proj;
            document.getElementById('edit_lien_proj').value = data.lien_proj;
            document.getElementById('edit_visibilite_proj').checked = (data.visible === true);
            
            let html = '';
            if(data.images && data.images.length) {
                data.images.forEach(img => {
                    html += '<div class="w3-col s4 w3-center w3-margin-bottom"><img src="<?= $base_url ?>/'+img.url_img+'" style="width:100%;height:80px;object-fit:cover" class="w3-round"><br><label><input type="checkbox" name="delete_images[]" value="'+img.id_img+'"> Supprimer</label></div>';
                });
            } else { html = '<p>Aucune image.</p>'; }
            currentImagesDiv.innerHTML = html;
        });
    } else { // Add mode
        modalTitle.textContent = 'Ajouter un projet';
        submitButton.textContent = 'Ajouter';
        actionInput.value = 'add_project';
        idInput.value = '';
        document.getElementById('edit_visibilite_proj').checked = false;
        currentImagesContainer.style.display = 'none';
        currentImagesDiv.innerHTML = '';
    }
}
</script>

<?php include $root . '/inc/footer.php'; ?>