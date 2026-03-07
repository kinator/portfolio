<?php
session_start();
// Permet d'activer l'affichage des erreurs
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

require_once dirname(__FILE__) . '/lib/project.lib.php';

if (GETPOST('debug') == true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

require_once(dirname(__FILE__) . '/class/authClass.php');

if (isset($_POST['connect'])) {
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['mesgs']['errors'][] = 'Erreur de sécurité (Session invalide).';
    } else {
        $uname = $_POST['uname'];
        $psw = $_POST['psw'];
        $user = authClass::authenticate($uname, $psw);
        if ($user) // Ajuster le test en fonction des besoins
        {
            $_SESSION['mesgs']['confirm'][] = 'Connexion réussie ' . $user['prenom_ens'] . ' ' . $user['nom_ens'];
            $_SESSION['login'] = $user['nom_util'];
            $_SESSION['titulaire'] = $user['titulaire_ens'];
            $_SESSION['admin'] = $user['admin'];
            $_SESSION['user'] = $user;
            $_SESSION['auth_token'] = bin2hex(random_bytes(32));
        }
        else{
            $_SESSION['mesgs']['errors'][] = 'Identification impossible';
        }
    }
}

$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
header('Location: ' . $base_url . '/');
