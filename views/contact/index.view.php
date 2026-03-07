<?php
include "$root/inc/head.php";
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<header class="contact-header">
    <h1 class="w3-jumbo w3-text-white"><b>Contactez-moi</b></h1>
    <p class="w3-xlarge w3-text-white">Discutons de votre projet</p>
</header>

<div class="w3-container w3-padding-64 w3-dark-grey w3-text-white">
    <div class="w3-content" style="max-width: 900px;">
        <h2 class="w3-center light">FORMULAIRE DE CONTACT</h2>
        <p class="w3-center"><em>Envoyez-moi un message</em></p>
        
        <div id="successMessage" class="w3-panel w3-green w3-display-container w3-padding-32 w3-center" style="display:none;">
          <h3>Message Envoyé!</h3>
          <p>Je reviendrai vers vous sous 48 heures.</p>
        </div>

        <div id="errorMessage" class="w3-panel w3-red w3-display-container w3-padding-16" style="display:none;">
            <p id="errorText"></p>
        </div>

        <div id="contactFormContainer" class="contact-card">
        <form id="contactForm" action="<?= $base_url ?>/contact/send" method="POST">
          <!-- Honeypot field -->
          <input type="text" name="website_hp" style="display:none !important" tabindex="-1" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

          <div class="form-group">
            <label class="form-label">Nom</label>
            <input class="form-input" type="text" required name="Name" value="<?= htmlspecialchars($_POST['Name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input class="form-input" type="text" required name="Email" id="contactEmail" value="<?= htmlspecialchars($_POST['Email'] ?? '') ?>">
            <div id="emailFeedback" class="w3-small" style="height: 20px; margin-top: 5px;"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Téléphone</label>
            <input class="form-input" type="tel" name="Phone" value="<?= htmlspecialchars($_POST['Phone'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Sujet</label>
            <input class="form-input" type="text" required name="Subject" value="<?= htmlspecialchars($_POST['Subject'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Message</label>
            <textarea class="form-input" name="Message" rows="5" required style="resize:vertical"><?= htmlspecialchars($_POST['Message'] ?? '') ?></textarea>
          </div>
          <button class="btn-project" type="submit">
            <i class="fa fa-paper-plane"></i> ENVOYER
          </button>
        </form>
        </div>

        <div class="w3-center w3-padding-32">
            <a href="<?= $base_url ?>/assets/doc/CV_Behani_Julien_dev_sys_db.pdf" class="btn-primary" download><i class="fa fa-download"></i> Télécharger mon CV</a>
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
        e.target.style.borderColor = '#ddd';
    } else if (regex.test(email)) {
        feedback.innerHTML = '<span class="w3-text-green"><i class="fa fa-check"></i> Email valide</span>';
        e.target.style.borderColor = 'green';
    } else {
        feedback.innerHTML = '<span class="w3-text-red"><i class="fa fa-times"></i> Email invalide</span>';
        e.target.style.borderColor = 'red';
    }
});

document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ENVOI...';
    
    document.getElementById('errorMessage').style.display = 'none';
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error('Réponse serveur invalide: ' + text.substring(0, 100) + '...');
        }
    })
    .then(data => {
        if (data.success) {
            document.getElementById('contactFormContainer').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
        } else {
            document.getElementById('errorText').textContent = data.message || 'Une erreur est survenue.';
            document.getElementById('errorMessage').style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('errorText').textContent = 'Une erreur technique est survenue.';
        document.getElementById('errorMessage').style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});
</script>

<?php
include "$root/inc/footer.php";
?>