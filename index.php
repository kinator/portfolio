<?php
$auth = require_once dirname(__FILE__) . '/lib/security.lib.php';
require_once dirname(__FILE__) . '/lib/project.lib.php';
include_once dirname(__FILE__) . '/vendor/autoload.php';

$root = dirname(__FILE__);

switch (GETPOST('page')) {
  case 'projets':
    include "$root/controllers/projets/index.controller.php";
    break;

    case 'bd':
    include "$root/controllers/database/index.controller.php";
    break;

    case null:
    include "$root/controllers/accueil/index.controller.php";
    break;

    default:
    include "$root/views/404.php";
    break;
}