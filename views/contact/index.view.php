<?php
include "$root/inc/head.php";
?>

<header class="w3-container w3-center" style="padding:128px 16px; background: linear-gradient(to right, #2c3e50, #4ca1af);">
    <h1 class="w3-jumbo w3-text-white" style="text-shadow:1px 1px 0 #444"><b>Contactez-moi</b></h1>
    <p class="w3-xlarge w3-text-white">Discutons de votre projet</p>
</header>

<div class="w3-container w3-padding-64 w3-white">
    <div class="w3-content">
        <h2 class="w3-center">FORMULAIRE DE CONTACT</h2>
        <p class="w3-center"><em>Envoyez-moi un message</em></p>
        
        <?php if ($messageSent): ?>
        <div class="w3-panel w3-green w3-display-container">
          <span onclick="this.parentElement.style.display='none'"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Succès!</h3>
          <p>Votre message a été envoyé.</p>
        </div>
        <?php endif; ?>

        <form action="<?= $base_url ?>/contact" method="POST" class="w3-container w3-card-4 w3-padding-16 w3-white">
          <div class="w3-section">
            <label>Nom</label>
            <input class="w3-input w3-border" type="text" required name="Name">
          </div>
          <div class="w3-section">
            <label>Email</label>
            <input class="w3-input w3-border" type="text" required name="Email">
          </div>
          <div class="w3-section">
            <label>Sujet</label>
            <input class="w3-input w3-border" type="text" required name="Subject">
          </div>
          <div class="w3-section">
            <label>Message</label>
            <input class="w3-input w3-border" type="text" required name="Message">
          </div>
          <button class="w3-button w3-black w3-section" type="submit">
            <i class="fa fa-paper-plane"></i> ENVOYER
          </button>
        </form>
    </div>
</div>

<?php
include "$root/inc/footer.php";
?>