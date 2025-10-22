<?php
/**
 * Point d'entrée principal de l'application CyberCigales
 * 
 * Ce fichier est le point d'entrée unique de l'application web.
 * Il gère le routing, l'autoloading des classes et l'exécution des contrôleurs.
 * 
 * Fonctionnement :
 * 1. Configuration de l'environnement (variables d'environnement, sessions)
 * 2. Récupération des paramètres d'URL (controller et action)
 * 3. Exécution du contrôleur approprié
 * 4. Affichage du résultat
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

$ROOT_DIR = dirname(__DIR__);

require $ROOT_DIR . '/vendor/autoload.php';
require_once $ROOT_DIR . '/includes/autoloader.php';

// Chargement des variables d'environnement depuis le fichier .env
$dotenv = Dotenv\Dotenv::createImmutable($ROOT_DIR . '/config');
$dotenv->load();

// Configuration sécurisée des sessions
if (session_status() === PHP_SESSION_NONE) {
    // Démarre la session uniquement si elle n'est pas déjà démarrée
    session_start([
        'use_strict_mode' => true,
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'None'
    ]);
}

// Configuration du rapport d'erreurs pour le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Récupération et nettoyage de l'URI demandée
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = '/' . trim($uri, '/');

// Récupération des paramètres de routing depuis l'URL
$S_controller = $_GET['controller'] ?? 'redirection';
$S_action = $_GET['action'] ?? 'openHomepage';

// Démarrage du buffer de sortie pour capturer le contenu
viewHandler::bufferStart();

// Exécution du contrôleur et de l'action demandés
$C_controller = new controllerHandler($S_controller, $S_action);
$C_controller->execute();

// Récupération du contenu généré et des paramètres
$displayContent = viewHandler::bufferCollect();
$A_params = $C_controller->getParams();

// Affichage final du contenu
echo $displayContent;