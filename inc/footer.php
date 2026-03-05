</div>
<?php
$errors = isset($_SESSION['mesgs']['errors']) ? $_SESSION['mesgs']['errors'] : [];
$confirms = isset($_SESSION['mesgs']['confirm']) ? $_SESSION['mesgs']['confirm'] : [];
$errors = json_encode($errors);
$confirms = json_encode($confirms);
unset($_SESSION['mesgs']['errors']);
unset($_SESSION['mesgs']['confirm']);

if (isset($db)) {
    $db = NULL;
}
?>

<script>
  var errors = <?= $errors ?>;
  var confirms = <?= $confirms ?>;

  setTimeout(function() {
    if (errors && errors.length > 0) {
      errors.forEach(error => {
        alert(error);
      });
    }
  }, 300);

  setTimeout(function() {
    if (confirms && confirms.length > 0) {
      confirms.forEach(confirm => {
        alert(confirm);
      });
    }
  }, 300);
</script>

<!-- Footer -->
<footer class="w3-container w3-padding-32 w3-center w3-black w3-xlarge">
  <div class="w3-section">
    <a href="https://github.com/kinator" target="_blank" class="w3-hover-text-grey w3-margin-right"><i class="fab fa-github fa-2x"></i></a>
    <a href="https://www.linkedin.com/in/julien-behani-929439353/" target="_blank" class="w3-hover-text-blue w3-margin-right"><i class="fab fa-linkedin fa-2x"></i></a>
    <a href="https://discord.com/users/712367050710056965" target="_blank" class="w3-hover-text-indigo"><i class="fab fa-discord fa-2x"></i></a>
  </div>
  <p class="w3-medium">Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank">w3.css</a></p>
  <p class="w3-medium">Hébergé par <strong>Kinator</strong></p>
  <p class="w3-medium"><a href="<?= $base_url ?>/mentions">Mentions légales</a></p>
</footer>

</body>
</html>