<?php
include $root . '/inc/head.php';
?>

<div class="dashboard-container">
    <!-- Import GitHub Section -->
    <div class="dashboard-card">
        <header class="dashboard-header">
            <h3>Importer depuis GitHub</h3>
        </header>
        <div class="dashboard-body">
            <form action="dashboard" method="post">
                <input type="hidden" name="action" value="import_github">
                <div class="form-group">
                    <label class="form-label">Nom d'utilisateur GitHub</label>
                    <input class="form-input" type="text" name="github_username" placeholder="Nom d'utilisateur GitHub" required>
                </div>
                <button type="submit" class="btn-primary">Importer</button>
            </form>
        </div>
    </div>

    <!-- Competences Management Section -->
    <div class="dashboard-card">
        <header class="dashboard-header">
            <h3>Gestion des Compétences</h3>
        </header>
        <div class="dashboard-body">
            <form action="dashboard" method="post" style="display:flex; gap:10px; margin-bottom:15px;">
                <input type="hidden" name="action" value="add_competence">
                <input class="form-input" type="text" name="name" placeholder="Nouvelle compétence" required style="flex:1;">
                <button type="submit" class="btn-primary">Ajouter</button>
            </form>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                <?php foreach ($all_competences as $comp): ?>
                    <span class="project-tag" style="padding:5px 10px; background:#f0f0f0; border-radius:15px; display:flex; align-items:center; gap:8px; border:1px solid #ddd;">
                        <?= htmlspecialchars($comp['name']) ?>
                        <a href="dashboard?action=delete_competence&id=<?= $comp['id_comp'] ?>" onclick="return confirm('Supprimer cette compétence ?')" style="color:#cc0000; text-decoration:none; font-weight:bold;">&times;</a>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Projects List Section -->
    <div class="dashboard-card">
        <div class="dashboard-header">
            <h2>Gestion des Projets</h2>
            <button onclick="openModal()" class="btn-primary">+ Nouveau Projet</button>
        </div>
        
        <div style="overflow-x:auto;">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th><a href="dashboard?sort=nom_proj&order=<?= ($sort === 'nom_proj' && $order === 'ASC') ? 'DESC' : 'ASC' ?>" style="color:inherit; text-decoration:none;">Nom du Projet<?= $sort === 'nom_proj' ? ($order === 'ASC' ? ' <i class="fa fa-sort-up"></i>' : ' <i class="fa fa-sort-down"></i>') : '' ?></a></th>
                        <th>Description</th>
                        <th><a href="dashboard?sort=visible&order=<?= ($sort === 'visible' && $order === 'ASC') ? 'DESC' : 'ASC' ?>" style="color:inherit; text-decoration:none;">Visible<?= $sort === 'visible' ? ($order === 'ASC' ? ' <i class="fa fa-sort-up"></i>' : ' <i class="fa fa-sort-down"></i>') : '' ?></a></th>
                        <th><a href="dashboard?sort=competences&order=<?= ($sort === 'competences' && $order === 'ASC') ? 'DESC' : 'ASC' ?>" style="color:inherit; text-decoration:none;">Compétences<?= $sort === 'competences' ? ($order === 'ASC' ? ' <i class="fa fa-sort-up"></i>' : ' <i class="fa fa-sort-down"></i>') : '' ?></a></th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($projects) > 0): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?= htmlspecialchars($project['nom_proj']) ?></td>
                                <td><?= htmlspecialchars($project['desc_proj']) ?></td>
                                <td style="text-align:center;">
                                    <?php if(!empty($project['visible'])): ?>
                                        <i class="fa fa-eye" style="color:green" title="Visible"></i>
                                    <?php else: ?>
                                        <i class="fa fa-eye-slash" style="color:grey" title="Masqué"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Display skills as tags -->
                                    <?php 
                                    $skills = trim($project['competences'] ?? '', '{}');
                                    if ($skills) {
                                        $skillsArray = explode(',', $skills);
                                        foreach ($skillsArray as $skill) {
                                            $skill = trim($skill, '"');
                                            echo '<span class="project-tag" style="font-size:0.7rem; padding:2px 8px; margin-right:4px;">' . htmlspecialchars($skill) . '</span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button onclick="openModal(<?= $project['id_proj'] ?>)" class="action-btn btn-edit">Éditer</button>
                                    <a href="dashboard?action=delete_project&id=<?= $project['id_proj'] ?>" class="action-btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px;">Aucun projet trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit/Add Project Modal -->
<div id="editModal" class="custom-modal">
    <div class="custom-modal-content" style="max-width:600px">
        <header class="modal-header"> 
            <h2 id="modal_title" style="margin:0; font-size:1.5rem;">Éditer le projet</h2>
            <button onclick="document.getElementById('editModal').style.display='none'" class="modal-close">&times;</button>
        </header>
        <div class="modal-body">
        <form id="edit_form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_project">
            <input type="hidden" name="id_proj" id="edit_id_proj">
            
            <div class="form-group">
                <label class="form-label">Nom du Projet</label>
                <input class="form-input" type="text" name="nom_proj" id="edit_nom_proj" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-input" name="desc_proj" id="edit_desc_proj" rows="3" style="resize:vertical"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Commentaire</label>
                <textarea class="form-input" name="commentaire_proj" id="edit_commentaire_proj" rows="3" style="resize:vertical"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Lien</label>
                <input class="form-input" type="text" name="lien_proj" id="edit_lien_proj">
            </div>
            
            <div class="form-group">
                <label class="form-label">Compétences</label>
                <div id="edit_competences" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap:10px; padding:10px; border:1px solid #eee; border-radius:8px; max-height: 150px; overflow-y: auto;">
                    <?php if (!empty($all_competences)): ?>
                        <?php foreach ($all_competences as $comp): ?>
                            <div>
                                <input type="checkbox" name="competences[]" value="<?= $comp['id_comp'] ?>" id="comp_<?= $comp['id_comp'] ?>" style="margin-right: 5px;">
                                <label for="comp_<?= $comp['id_comp'] ?>"><?= htmlspecialchars($comp['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucune compétence trouvée dans la base de données.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="display:inline-block; margin-right:10px;">Visible</label>
                <input type="checkbox" name="visibilite_proj" id="edit_visibilite_proj" value="1" style="transform: scale(1.5);">
            </div>

            <div id="current_images_container" class="form-group">
                <label class="form-label">Images actuelles</label>
                <div id="current_images" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap:10px; padding:10px; border:1px solid #eee; border-radius:8px;"></div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Ajouter des images</label>
                <input class="form-input" type="file" name="new_images[]" multiple accept="image/*">
            </div>
            
            <div style="text-align:right; margin-top:20px;">
                <button type="submit" id="modal_submit_button" class="btn-primary">Sauvegarder</button>
            </div>
        </form>
        </div>
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

            // Reset all competence checkboxes first
            document.querySelectorAll('#edit_competences input[type="checkbox"]').forEach(cb => cb.checked = false);
            // Check the ones for the project
            if (data.competences && data.competences.length) {
                data.competences.forEach(compId => {
                    const cb = document.getElementById('comp_' + compId);
                    if (cb) cb.checked = true;
                });
            }
            
            let html = '';
            if(data.images && data.images.length) {
                data.images.forEach(img => {
                    html += '<div style="text-align:center;"><img src="<?= $base_url ?>/'+img.url_img+'" style="width:100%;height:80px;object-fit:cover;border-radius:4px;"><br><label style="font-size:0.8rem;"><input type="checkbox" name="delete_images[]" value="'+img.id_img+'"> Supprimer</label></div>';
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
        document.querySelectorAll('#edit_competences input[type="checkbox"]').forEach(cb => cb.checked = false);
    }
}
</script>

<?php include $root . '/inc/footer.php'; ?>