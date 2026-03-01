<?php
include "$root/inc/head.php";
?>

<header class="w3-container w3-center" style="padding:96px 16px; background: linear-gradient(to right, #2c3e50, #4ca1af);">
  <h1 class="w3-jumbo w3-text-white" style="text-shadow:1px 1px 0 #444"><b>Bienvenue sur mon Portfolio</b></h1>
  <p class="w3-xlarge w3-text-white">Développeur Web & Créateur de Solutions</p>
  <a href="<?= $base_url ?>/projets" class="w3-button w3-white w3-padding-large w3-large w3-margin-top w3-hover-opacity">Voir mes projets</a>
</header>

<!-- About Section -->
<div class="w3-container w3-padding-64 w3-blue-grey" id="about">
  <div class="w3-content">
    <h2 class="w3-center">À PROPOS DE MOI</h2>
    <p class="w3-center"><em>Passionné par le code et les systèmes</em></p>
    <div class="w3-row w3-padding-32">
      <div class="w3-col m6 w3-padding-large w3-hide-small">
        <div class="w3-center w3-padding-64">
          <i class="fa fa-code fa-5x w3-text-white"></i>
        </div>
      </div>

      <div class="w3-col m6 w3-padding-large">
        <p>Bonjour ! Je suis un développeur web / application / base de données / système passionné par la création d'expériences numériques intuitives et dynamiques, ainsi que d'outils utiles et faciles d'utilisation. Avec une solide base en langages web, application et système, je transforme des idées en réalité.</p>
        <p>Ce portfolio présente une sélection de mes travaux récents. N'hésitez pas à explorer la section projets pour voir ce que j'ai réalisé. Je suis toujours à la recherche de nouveaux défis et opportunités pour apprendre et grandir.</p>
      </div>
    </div>
  </div>
</div>

<!-- Skills Section -->
<div class="w3-row-padding w3-center w3-dark-grey w3-text-white w3-padding-64">
  <h2 class="w3-center light">MES COMPÉTENCES</h2>
  <p class="w3-center"><em>Ce que je fais</em></p><br>
  <div class="w3-quarter w3-section">
    <i class="fab fa-php fa-3x w3-margin-bottom w3-text-indigo"></i>
    <p class="w3-large"><b>PHP & SQL</b></p>
    <p>Développement back-end robuste et performant.</p>
  </div>
  <div class="w3-quarter w3-section">
    <i class="fa fa-code fa-3x w3-margin-bottom w3-text-orange"></i>
    <p class="w3-large"><b>HTML, CSS & JS</b></p>
    <p>Création d'interfaces front-end réactives et modernes.</p>
  </div>
  <div class="w3-quarter w3-section">
    <i class="fab fa-git-alt fa-3x w3-margin-bottom w3-text-white"></i>
    <p class="w3-large"><b>Git & DevOps</b></p>
    <p>Workflow de développement et déploiement continu.</p>
  </div>
  <div class="w3-quarter w3-section">
    <i class="fa fa-server fa-3x w3-margin-bottom w3-text-teal"></i>
    <p class="w3-large"><b>Système & Réseau</b></p>
    <p>Administration et optimisation d'infrastructures.</p>
  </div>
</div>

<div class="w3-container w3-center w3-padding-64 w3-blue-grey">
  <h2 class="w3-center">TAGS</h2>
  <p class="w3-center"><em>Mots clés</em></p><br>
  <div class="w3-row-padding w3-center" style="display:flex; flex-wrap:wrap; justify-content:center;">
  <?php foreach ($tags as $tag) : ?>
    <div class="w3-quarter w3-section">
      <div class="w3-card w3-round-large w3-dark-grey w3-text-white w3-padding w3-hover-shadow" style="height:100%">
        <p class="w3-large w3-text-light-blue"><b><i class="fa fa-hashtag"></i> <?= sanitize($tag['id_comp']) ?></b></p>
        <p class="w3-small"><?= sanitize($tag['description']) ?></p>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
</div>


<!-- University Section -->
<div class="w3-container w3-padding-64 w3-dark-grey w3-center">
  <div class="w3-content">
    <h2 class="w3-center light">MON PARCOURS</h2>
    <p class="w3-text-light-grey"><em>Formation & Diplômes</em></p>
    
    <div class="w3-row w3-padding-32" style="display:flex; align-items:center; flex-wrap:wrap">
      <div class="w3-col m6 w3-padding-large w3-hide-small">
        <a href="https://www.iut-littoral.fr/" target="_blank"><img src="<?= $base_url ?>/assets/img/logo_iut.png" alt="Logo IUT" style="width:75%"></a>
      </div>
      <div class="w3-col m6 w3-padding-large" style="text-align:left;">
        <h3 class="w3-center light">Institut Universitaire de Technologie</h3>
        <p class="w3-large w3-center">BUT Informatique (bac +3)</p>
        <p>Spécialisation en développement logiciel et architectures web. Durant ces années, j'ai acquis des bases solides en algorithmique, bases de données et gestion de projet agile.</p>
        <p>J'ai participé activement aux événements de l'IUT et aux projets académiques, et été nommé président de l'association étudiantes JrCanDev (maintenant président honoraire).</p>
      </div>
    </div>

    <div class="w3-row w3-padding-32" style="display:flex; align-items:center; flex-wrap:wrap">
      <div class="w3-col m6 w3-padding-large w3-hide-small">
        <div class="w3-center"><img src="<?= $base_url ?>/assets/img/TOEIC_logo.png" alt="Logo IUT" style="width:75%"></div>
      </div>
      <div class="w3-col m6 w3-padding-large" style="text-align:left;">
        <h3 class="w3-center light">Certification TOEIC</h3>
        <p class="w3-large w3-center">Score : 990/990</p>
        <p>Obtention du score maximal au test TOEIC Listening & Reading. Cette performance valide un haut niveau et atteste de ma capacité à travailler couramment en anglais dans un contexte professionnel international.</p>
      </div>
    </div>
  </div>
</div>

<!-- Hobbies Section -->
<div class="w3-container w3-padding-64 w3-blue-grey w3-center">
  <h2>MES PASSIONS</h2>
  <p class="w3-text-light-grey"><em>Ce qui m'anime en dehors du code</em></p>
  <div class="w3-row-padding w3-padding-32">
    <div class="w3-quarter w3-section">
      <i class="fa fa-gamepad fa-3x w3-text-red w3-margin-bottom"></i>
      <p class="w3-large">Gaming</p>
      <p>Stratégie et réflexion.</p>
    </div>
    <div class="w3-quarter w3-section">
      <i class="fa fa-camera fa-3x w3-text-blue w3-margin-bottom"></i>
      <p class="w3-large">Photographie</p>
      <p>Capturer l'instant.</p>
    </div>
    <div class="w3-quarter w3-section">
      <i class="fa fa-plane fa-3x w3-text-yellow w3-margin-bottom"></i>
      <p class="w3-large">Voyage</p>
      <p>Découverte de nouvelles cultures.</p>
    </div>
    <div class="w3-quarter w3-section">
      <i class="fa fa-music fa-3x w3-text-purple w3-margin-bottom"></i>
      <p class="w3-large">Musique</p>
      <p>Inspiration quotidienne.</p>
    </div>
  </div>
</div>

<!-- Contact Section -->
<div class="w3-container w3-padding-64 w3-dark-grey w3-text-white w3-center" id="contact">
  <h2 class="w3-center light">CONTACTEZ-MOI</h2>
  <p class="w3-center"><em>Travaillons ensemble !</em></p>
  <div class="w3-large w3-padding-32">
    <p>Vous avez un projet en tête ou vous souhaitez simplement dire bonjour ? N'hésitez pas à m'envoyer un email. Si la rédaction d'email n'est pas votre truc, alors mon discord est toujours ouvert</p>
    <div class="w3-section">
      <a href="https://github.com/kinator" target="_blank" class="w3-hover-text-grey w3-margin-right"><i class="fab fa-github fa-2x"></i></a>
      <a href="https://www.linkedin.com/in/julien-behani-929439353/" target="_blank" class="w3-hover-text-blue w3-margin-right"><i class="fab fa-linkedin fa-2x"></i></a>
      <a href="https://discord.com/users/712367050710056965" target="_blank" class="w3-hover-text-indigo"><i class="fab fa-discord fa-2x"></i></a>
    </div>
    <a href="<?= $base_url ?>/contact" class="w3-button w3-black w3-padding-large w3-large w3-margin-top w3-hover-opacity"><i class="fa fa-paper-plane"></i> ENVOYER UN MESSAGE</a>
  </div>
</div>

<?php
include "$root/inc/footer.php";
?>