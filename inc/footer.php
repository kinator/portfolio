</div>
<?php
$errors = isset($_SESSION['mesgs']['errors']) ? $_SESSION['mesgs']['errors'] : [];
$confirms = isset($_SESSION['mesgs']['confirm']) ? $_SESSION['mesgs']['confirm'] : [];
$errors = json_encode($errors);
$confirms = json_encode($confirms);
unset($_SESSION['mesgs']['errors']);
unset($_SESSION['mesgs']['confirm']);

if ($db) {
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
  <a href="https://github.com/JrCanDev/GDI" target="_blank"><i class="fab fa-github w3-hover-opacity" aria-hidden="true"></i></a>
  <p class="w3-medium">Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank">w3.css</a></p>
  <p class="w3-medium">Hébergé par <a href="https://github.com/JrCanDev" target="_blank">JrCanDev</a></p>
</footer>

</body>
</html>