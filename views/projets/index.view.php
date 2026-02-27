<?php
include "$root/inc/head.php";
?>

<header class="w3-container w3-center" style="padding:128px 16px; background: linear-gradient(to right, #2c3e50, #4ca1af);">
  <h1 class="w3-jumbo w3-text-white" style="text-shadow:1px 1px 0 #444"><b>Mes Projets</b></h1>
  <p class="w3-xlarge w3-text-white">Découvrez mes réalisations</p>
</header>

<div class="w3-container w3-padding-64 w3-white">
  <div class="w3-content">
    <h2 class="w3-center">GALERIE DE PROJETS</h2>
    <p class="w3-center"><em>Un aperçu de mon travail</em></p>
    
    <div class="w3-row-padding w3-padding-32" style="margin:0 -16px">
      <?php foreach ($projects as $project): ?>
        <div class="w3-third w3-margin-bottom">
          <div class="w3-card-4" style="height: 100%; display: flex; flex-direction: column;">
            <img src="<?= $project['image'] ?>" alt="<?= $project['title'] ?>" style="width:100%" class="w3-hover-opacity">
            <div class="w3-container w3-white" style="flex-grow: 1;">
              <h3><b><?= $project['title'] ?></b></h3>
              <p><?= $project['description'] ?></p>
              <div class="w3-margin-bottom">
                <?php foreach ($project['tags'] as $tag): ?>
                  <span class="w3-tag w3-light-grey w3-small w3-margin-bottom"><?= $tag ?></span>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="w3-container w3-white w3-padding">
              <button onclick="document.getElementById('modal-<?= $project['id'] ?>').style.display='block'" class="w3-button w3-black w3-block"><i class="fa fa-eye"></i> Voir</button>
            </div>
          </div>

          <!-- Modal -->
          <div id="modal-<?= $project['id'] ?>" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom w3-card-4">
              <header class="w3-container w3-blue-grey"> 
                <span onclick="document.getElementById('modal-<?= $project['id'] ?>').style.display='none'" class="w3-button w3-display-topright">&times;</span>
                <h2><?= $project['title'] ?></h2>
              </header>
              <div class="w3-container w3-padding">
                <p><?= $project['description'] ?></p>
                <div class="w3-row-padding" style="margin:0 -16px">
                  <?php foreach ($project['images'] as $img): ?>
                    <div class="w3-half w3-margin-bottom">
                      <img src="<?= $img ?>" style="width:100%; cursor:pointer" onclick="window.open(this.src, '_blank')" alt="<?= $project['title'] ?>">
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="w3-margin-bottom">
                  <?php foreach ($project['tags'] as $tag): ?>
                    <span class="w3-tag w3-light-grey w3-small w3-margin-right"><?= $tag ?></span>
                  <?php endforeach; ?>
                </div>
                <?php if ($project['link']): ?>
                  <a href="<?= $project['link'] ?>" target="_blank" class="w3-button w3-black w3-block"><i class="fa fa-external-link"></i> Visiter le site</a>
                <?php endif; ?>
              </div>
              <footer class="w3-container w3-blue-grey w3-padding">
                <button class="w3-button w3-right w3-white w3-border" onclick="document.getElementById('modal-<?= $project['id'] ?>').style.display='none'">Fermer</button>
              </footer>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php
include "$root/inc/footer.php";
?>