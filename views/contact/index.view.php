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
        <div class="w3-panel w3-green w3-display-container w3-padding-32 w3-center">
          <h3>Message Envoyé!</h3>
          <p>Je reviendrai vers vous sous 48 heures.</p>
        </div>
        <?php else: ?>

        <?php if (!empty($errorMessage)): ?>
        <div class="w3-panel w3-red w3-display-container w3-padding-16">
            <p><?= htmlspecialchars($errorMessage) ?></p>
        </div>
        <?php endif; ?>

        <form action="<?= $base_url ?>/contact" method="POST" class="w3-container w3-card-4 w3-padding-16 w3-white">
          <!-- Honeypot field -->
          <input type="text" name="website_hp" style="display:none !important" tabindex="-1" autocomplete="off">

          <div class="w3-section">
            <label>Nom</label>
            <input class="w3-input w3-border" type="text" required name="Name" value="<?= htmlspecialchars($_POST['Name'] ?? '') ?>">
          </div>
          <div class="w3-section">
            <label>Email</label>
            <input class="w3-input w3-border" type="text" required name="Email" id="contactEmail" value="<?= htmlspecialchars($_POST['Email'] ?? '') ?>">
            <div id="emailFeedback" class="w3-small" style="height: 20px; margin-top: 5px;"></div>
          </div>
          <div class="w3-section">
            <label>Téléphone</label>
            <input class="w3-input w3-border" type="tel" name="Phone" value="<?= htmlspecialchars($_POST['Phone'] ?? '') ?>">
          </div>
          <div class="w3-section">
            <label>Sujet</label>
            <input class="w3-input w3-border" type="text" required name="Subject" value="<?= htmlspecialchars($_POST['Subject'] ?? '') ?>">
          </div>
          <div class="w3-section">
            <label>Message</label>
            <textarea class="w3-input w3-border" name="Message" rows="5" required style="resize:vertical"><?= htmlspecialchars($_POST['Message'] ?? '') ?></textarea>
          </div>
          <button class="w3-button w3-black w3-section" type="submit">
            <i class="fa fa-paper-plane"></i> ENVOYER
          </button>
        </form>
        <?php endif; ?>

        <div class="w3-center w3-padding-32">
            <a href="<?= $base_url ?>/doc/CV_Behani_Julien_dev_sys_db.pdf" class="w3-button w3-black w3-round" download><i class="fa fa-download"></i> Télécharger mon CV</a>
        </div>
    </div>
</div>

<script>
document.getElementById('contactEmail')?.addEventListener('input', function(e) {
    const email = e.target.value;
    const feedback = document.getElementById('emailFeedback');
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email.length === 0) {
        feedback.innerHTML = '';
        e.target.classList.remove('w3-border-green', 'w3-border-red');
    } else if (regex.test(email)) {
        feedback.innerHTML = '<span class="w3-text-green"><i class="fa fa-check"></i> Email valide</span>';
        e.target.classList.add('w3-border-green');
        e.target.classList.remove('w3-border-red');
    } else {
        feedback.innerHTML = '<span class="w3-text-red"><i class="fa fa-times"></i> Email invalide</span>';
        e.target.classList.add('w3-border-red');
        e.target.classList.remove('w3-border-green');
    }
});
</script>

<?php
include "$root/inc/footer.php";
?>