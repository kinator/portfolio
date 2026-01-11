<?php
include "$root/inc/head.php";
?>

<!-- Hero Section -->
<div class="w3-container w3-light-grey w3-center w3-padding-64">
    <h1 class="w3-jumbo"><b>Bienvenue sur mon Portfolio</b></h1>
    <p class="w3-large">Développeur Web & Créateur de Solutions</p>
    <a href="<?= $root; ?>/projets" class="w3-button w3-black w3-padding-large w3-large w3-margin-top">Voir mes projets</a>
</div>

<!-- About Section -->
<div class="w3-content w3-padding-64 w3-container">
    <h2 class="w3-center">À PROPOS</h2>
    <p class="w3-center"><em>Passionné par le code et le design</em></p>
    <div class="w3-row">
        <div class="w3-col m6 w3-center w3-padding-large">
            <!-- Placeholder icon for profile image -->
            <i class="fa fa-user w3-jumbo w3-text-grey"></i>
        </div>
        <div class="w3-col m6 w3-padding-large">
            <p>Bonjour ! Je suis un développeur web passionné par la création d'expériences numériques intuitives et dynamiques. Avec une solide base en PHP, HTML, CSS et JavaScript, je transforme des idées en réalité.</p>
            <p>Ce portfolio présente une sélection de mes travaux récents. N'hésitez pas à explorer la section projets pour voir ce que j'ai réalisé.</p>
        </div>
    </div>
</div>

<!-- Skills Section -->
<div class="w3-row w3-center w3-dark-grey w3-padding-64">
    <div class="w3-third w3-section">
        <span class="w3-xlarge">PHP & SQL</span><br>
        Back-end Development
    </div>
    <div class="w3-third w3-section">
        <span class="w3-xlarge">HTML, CSS & JS</span><br>
        Front-end Design
    </div>
    <div class="w3-third w3-section">
        <span class="w3-xlarge">Git & DevOps</span><br>
        Workflow & Deployment
    </div>
</div>

<!-- Contact Section -->
<div class="w3-content w3-container w3-padding-64">
    <h3 class="w3-center">CONTACT</h3>
    <p class="w3-center"><em>Travaillons ensemble !</em></p>
    <div class="w3-center w3-padding-large">
        <p>Vous avez un projet en tête ou vous souhaitez simplement dire bonjour ?</p>
        <a href="mailto:contact@example.com" class="w3-button w3-black"><i class="fa fa-envelope"></i> Me contacter</a>
    </div>
</div>

<?php
include "$root/inc/footer.php";
?>