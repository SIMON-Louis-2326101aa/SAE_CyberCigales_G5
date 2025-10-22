<?php
$ROOT_DIR = dirname(__DIR__);

require $ROOT_DIR . '/vendor/autoload.php';
require_once $ROOT_DIR . '/includes/autoloader.php';

$dotenv = Dotenv\Dotenv::createImmutable($ROOT_DIR . '/config');
$dotenv->load();

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

$S_controller = $_GET['controller'] ?? 'redirection';
$S_action = $_GET['action'] ?? 'openHomepage';

viewHandler::bufferStart();
// Exécution du contrôleur et de l'action
$C_controller = new controllerHandler($S_controller, $S_action);
$C_controller->execute();

// Récupère le contenu tamponné
$displayContent = viewHandler::bufferCollect();
$A_params = $C_controller->getParams();


echo $displayContent;