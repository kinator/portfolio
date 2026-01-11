<?php
$db = require "$root/lib/pdo.php";
$tags = getTags($db);
include "$root/views/accueil/index.view.php";
