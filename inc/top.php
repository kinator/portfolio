<?php
if (isset($_SESSION['user']) && file_exists(dirname(__FILE__, 2) . '/class/authClass.php')) {
  require_once dirname(__FILE__, 2) . '/class/authClass.php';
}
?>

<!-- Navbar -->
<div class="w3-bar w3-white w3-card" id="myNavbar">
    <a href="<?= $base_url ?>/" class="w3-bar-item w3-button w3-wide"><b>PORTFOLIO</b> JULIEN BEHANI</a>
    <!-- Right-sided navbar links -->
    <div class="w3-right w3-hide-small">
      <a href="<?= $base_url ?>/" class="w3-bar-item w3-button"><i class="fa fa-home w3-margin-right"></i>ACCUEIL</a>
      <a href="<?= $base_url ?>/projets" class="w3-bar-item w3-button"><i class="fa fa-folder-open w3-margin-right"></i>PROJETS</a>
      <?php if (isset($_SESSION['user']) && authClass::checkPriviledAdmin($_SESSION['user']['nom_util'])) { ?>
        <a href="<?= $base_url ?>/bd" class="w3-bar-item w3-button"><i class="fa fa-database w3-margin-right"></i>BASE DE DONNÉES</a>
      <?php } ?>
      <?php if (isset($_SESSION['user'])) : ?>
        <div class="w3-dropdown-hover w3-right">
          <button class="w3-button"><i class="fa fa-user w3-margin-right"></i><?= $_SESSION['user']['nom_ens'] == 'admin_nom' ? 'ADMIN' : strtoupper(sanitize($_SESSION['user']['prenom_ens'])) ?> <i class="fa fa-caret-down"></i></button>
          <div class="w3-dropdown-content w3-bar-block w3-card-4">
            <a href="<?= $base_url ?>/disconnect.php" class="w3-bar-item w3-button">Déconnexion</a>
          </div>
        </div>
      <?php else : ?>
        <a href="<?= $base_url ?>/login.php" class="w3-bar-item w3-button"><i class="fa fa-sign-in-alt w3-margin-right"></i>CONNEXION</a>
      <?php endif; ?>
    </div>
    <!-- Hide right-floated links on small screens and replace them with a menu icon -->
    <a href="javascript:void(0)" class="w3-bar-item w3-button w3-right w3-hide-large w3-hide-medium" onclick="w3_open()">
      <i class="fa fa-bars"></i>
    </a>
</div>

<!-- Sidebar on small screens when clicking the menu icon -->
<nav class="w3-sidebar w3-bar-block w3-black w3-card w3-animate-left w3-hide-medium w3-hide-large" style="display:none" id="mySidebar">
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-bar-item w3-button w3-large w3-padding-16">Fermer ×</a>
  <a href="<?= $base_url ?>/" onclick="w3_close()" class="w3-bar-item w3-button">ACCUEIL</a>
  <a href="<?= $base_url ?>/projets" onclick="w3_close()" class="w3-bar-item w3-button">PROJETS</a>
  <?php if (isset($_SESSION['user']) && authClass::checkPriviledAdmin($_SESSION['user']['nom_util'])) { ?>
    <a href="<?= $base_url ?>/bd" onclick="w3_close()" class="w3-bar-item w3-button">BASE DE DONNÉES</a>
  <?php } ?>
  <?php if (isset($_SESSION['user'])) : ?>
    <a href="<?= $base_url ?>/disconnect.php" class="w3-bar-item w3-button">DÉCONNEXION</a>
  <?php else : ?>
    <a href="<?= $base_url ?>/login.php" class="w3-bar-item w3-button">CONNEXION</a>
  <?php endif; ?>
</nav>

<script>
// Toggle between showing and hiding the sidebar when clicking the menu icon
var mySidebar = document.getElementById("mySidebar");

function w3_open() {
  if (mySidebar.style.display === 'block') {
    mySidebar.style.display = 'none';
  } else {
    mySidebar.style.display = 'block';
  }
}

// Close the sidebar with the close button
function w3_close() {
    mySidebar.style.display = "none";
}
</script>