<?php
require_once __DIR__ . '/includes/autoloader.php';
require_once __DIR__ . '/includes/constant.php';

// Récupérer l'URI demandée
ini_set('display_errors', 1);
error_reporting(E_ALL);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = '/' . trim($uri, '/');

// Router selon l’URL
switch ($uri) {
    case '/':
    case '/home':
        require_once __DIR__ . '/Modules/controller/homepageController.php';
        (new HomePageController())->index();
        break;

    case '/login':
        require_once __DIR__ . '/Modules/controller/UserController.php';
        (new UserController())->login();
        break;

    case '/register':
        require_once __DIR__ . '/Modules/controller/UserController.php';
        (new UserController())->register();
        break;

    case '/logout':
        require_once __DIR__ . '/Modules/controller/UserController.php';
        (new UserController())->logout();
        break;

	case '/passforgotten':
		require_once __DIR__ . '/Modules/controller/UserController.php';
        (new UserController())->passforgotten();
        break;

    default:
        require_once __DIR__ . '/public/404.php';
        break;
}
