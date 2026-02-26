<?php
session_start();
require_once dirname(__FILE__) . '/lib/project.lib.php';
include_once dirname(__FILE__) . '/vendor/autoload.php';

$root = dirname(__FILE__);
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

// Get the page from the query string provided by .htaccess, trimming slashes.
$requestPath = isset($_GET['q']) ? trim($_GET['q'], '/') : '';
$page = $requestPath === '' ? null : $requestPath;

// Define all routes and whether they require authentication
$routes = [
    'projets'  => ['controller' => "$root/controllers/projets/index.controller.php", 'auth' => false],
    'contact'  => ['controller' => "$root/controllers/contact/index.controller.php", 'auth' => false],
    'bd'       => ['controller' => "$root/controllers/database/index.controller.php", 'auth' => true],
    'dashboard'=> ['controller' => "$root/controllers/dashboard/index.controller.php", 'auth' => true],
    'import_github'=> ['controller' => "$root/controllers/dashboard/import_github.controller.php", 'auth' => true],
    'delete_project'=> ['controller' => "$root/controllers/dashboard/delete_project.controller.php", 'auth' => true],
    null       => ['controller' => "$root/controllers/accueil/index.controller.php", 'auth' => false]
];

// Check if the page is a defined route
if (array_key_exists($page, $routes)) {
    $route = $routes[$page];

    // If authentication is required for the route, include the security library
    if ($route['auth']) {
        $auth = require_once dirname(__FILE__) . '/lib/security.lib.php';
    }

    // Include the controller for the page
    include $route['controller'];
} else {
    // If the page is not found, show a 404 error
    http_response_code(404);
    include "$root/views/404.php";
}
