<?php
require_once __DIR__ . '/includes/autoloader.php';
require_once __DIR__ . '/includes/constant.php';

if (session_status() === PHP_SESSION_NONE) {
    // Démarre la session uniquement si elle n'est pas déjà démarrée
    session_start([
        'use_strict_mode' => true,
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'None'
    ]);
}

// Récupérer l'URI demandée
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = '/' . trim($uri, '/');

$S_controller = $_GET['controller'] ?? 'homePage';
$S_action = $_GET['action'] ?? 'login';

ViewHandler::bufferStart();
// Exécution du contrôleur et de l'action
$C_controller = new ControllerHandler($S_controller, $S_action);
$C_controller->execute();

// Récupère le contenu tamponné
$displayContent = ViewHandler::bufferCollect();
$A_params = $C_controller->getParams();


echo $displayContent;

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
