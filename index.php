<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/' || $uri === '/home') {
    echo "<h1>Accueil</h1><p><a href='/login'>Connexion</a> | <a href='/register'>Inscription</a></p>";
} elseif ($uri === '/login') {
    require_once __DIR__ . '/../Modules/controller/UserController.php';
    $controller = new UserController();
    $controller->login();
} elseif ($uri === '/register') {
    require_once __DIR__ . '/../Modules/controller/UserController.php';
    $controller = new UserController();
    $controller->register();
} elseif ($uri === '/logout') {
    require_once __DIR__ . '/../Modules/controller/UserController.php';
    $controller = new UserController();
    $controller->logout();
} else {
    require_once __DIR__ . '/404.php';
}
