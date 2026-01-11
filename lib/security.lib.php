<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once dirname(__FILE__) . '/../class/authClass.php';
$authorized = authClass::is_auth($_SESSION);
return $authorized;
