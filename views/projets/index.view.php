<?php
include "$root/inc/head.php";
?>

<header class="projects-header">
  <h1 class="w3-jumbo"><b>Mes Projets</b></h1>
  <p class="w3-xlarge w3-text-white">Découvrez mes réalisations</p>
</header>

<div class="w3-container w3-padding-64 w3-dark-grey w3-text-white">
  <div class="w3-content">
    <h2 class="w3-center light">GALERIE DE PROJETS</h2>
    <p class="w3-center"><em>Un aperçu de mon travail</em></p>
    
    <div class="projects-grid">
      <?php foreach ($projects as $project): ?>
        <div class="project-card">
          <div style="overflow: hidden;">
            <img src="<?= $project['image'] ?>" alt="<?= $project['title'] ?>" class="project-card-image">
          </div>
          <div class="project-card-content">
            <h3 class="project-card-title"><?= $project['title'] ?></h3>
            <p class="project-card-desc"><?= $project['description'] ?></p>
            <div class="project-tags">
              <?php foreach ($project['tags'] as $tag): ?>
                <span class="project-tag"><?= $tag ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="project-card-footer">
            <button onclick="document.getElementById('modal-<?= $project['id'] ?>').style.display='block'" class="btn-project"><i class="fa fa-eye"></i> Voir le projet</button>
          </div>
        </div>

        <!-- Modal -->
        <div id="modal-<?= $project['id'] ?>" class="custom-modal">
          <div class="custom-modal-content">
            <header class="modal-header"> 
              <h2 style="margin:0; font-size:1.5rem;"><?= $project['title'] ?></h2>
              <button onclick="document.getElementById('modal-<?= $project['id'] ?>').style.display='none'" class="modal-close">&times;</button>
            </header>
            <div class="modal-body">
              <p style="font-size: 1.1rem; line-height: 1.6;"><?= $project['description'] ?></p>
              
              <div class="modal-gallery">
                <?php foreach ($project['images'] as $img): ?>
                    <img src="<?= $img ?>" onclick="openLightbox(this.src)" alt="<?= $project['title'] ?>">
                <?php endforeach; ?>
              </div>

              <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <h4 style="margin-bottom: 15px; font-size: 1rem; color: #666;">Technologies utilisées :</h4>
                <div class="project-tags">
                <?php foreach ($project['tags'] as $tag): ?>
                  <span class="project-tag"><?= $tag ?></span>
                <?php endforeach; ?>
                </div>
              </div>

              <?php if ($project['link']): ?>
                <div style="margin-top: 30px;">
                  <a href="<?= $project['link'] ?>" target="_blank" class="btn-project" style="display:inline-block; text-align:center; text-decoration:none;"><i class="fa fa-external-link"></i> Visiter le site</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox-modal" class="custom-modal" style="z-index: 1100;" onclick="this.style.display='none'">
    <span class="modal-close" style="position:fixed; top:20px; right:30px; z-index:1101;">&times;</span>
    <div style="display:flex; justify-content:center; align-items:center; height:100%; padding:20px;">
        <img id="lightbox-image" src="" style="max-width:100%; max-height:100%; object-fit:contain; border-radius:8px; box-shadow: 0 5px 25px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">
    </div>
</div>

<script>
function openLightbox(src) {
    document.getElementById('lightbox-image').src = src;
    document.getElementById('lightbox-modal').style.display = 'block';
}
</script>

<?php
include "$root/inc/footer.php";
?>