<?php

/**
 * Point d'entrée de l'application (public/index.php)
 * - Résout les chemins depuis la racine via $ROOT_DIR.
 * - Charge Composer et l'autoloader interne.
 * - Charge les variables d'environnement depuis /config/.env.
 * - Démarre la session de manière sécurisée.
 * - Configure le reporting d'erreurs selon APP_ENV.
 * - Route via controllerHandler et rend via viewHandler.
 * - Écrit des logs de debug discrets en commentaires HTML.
 */

declare(strict_types=1);

use SAE_CyberCigales_G5\includes\ControllerHandler;
use SAE_CyberCigales_G5\includes\ViewHandler;
use SAE_CyberCigales_G5\Modules\model\GameProgressModel;

// ============================================================
//  CONFIGURATION DU LOG SERVEUR (AVANT TOUT)
// ============================================================

// Détermine le dossier racine du projet
$ROOT_DIR = dirname(__DIR__);

require_once $ROOT_DIR . '/includes/functions.php';

// ============================================================
//  Configuration des logs (1 fichier par jour, max 7 fichiers)
// ============================================================

// Dossier de logs
$logDir = $ROOT_DIR . '/var/log';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0775, true);
}

// Nom du fichier du jour
$today = date('Y-m-d');
$logFile = "{$logDir}/app-{$today}.log";

// Active le log PHP côté serveur
// Change dynamiquement le fichier de log PHP
ini_set('log_errors', '1');
ini_set('error_log', $logFile);

registerLogRotation($logDir, $logFile);

/* ============================================================
    Session sécurisée
   ============================================================ */

// Configuration sécurisée des sessions
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $forceSecure = isset($_ENV['FORCE_SECURE']) && $_ENV['FORCE_SECURE'] === '1';
    $cookieSecure = $forceSecure ? true : $isHttps;
// Si on n’est pas en HTTPS → on ne met pas SameSite=None
    $cookieSameSite = $cookieSecure ? 'None' : 'Lax';
    session_start([
        'use_strict_mode' => true,
        'cookie_httponly' => true,
        'cookie_secure'   => $cookieSecure,
        'cookie_samesite' => $cookieSameSite,
    ]);
    if (function_exists('log_console')) {
        log_console('Session démarrée', 'ok');
    }
}

set_error_handler(function ($severity, $message, $file, $line) {

    // Ignore les erreurs masquées par @
    if (!(error_reporting() & $severity)) {
        return false;
    }
    if (function_exists('log_console')) {
        log_console("$message @ $file:$line", 'warn');
    }
    return false;
});

set_exception_handler(function (Throwable $e) {
    if (function_exists('log_console')) {
        log_console($e->getMessage() . " @ {$e->getFile()}:{$e->getLine()}", 'error', [
            'trace' => substr($e->getTraceAsString(), 0, 2000),
        ]);
    }
});

register_shutdown_function(function () {

    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        log_console($e['message'] . " @ {$e['file']}:{$e['line']}", 'error');
    }
});
/* ============================================================
    Chargements initiaux
   ============================================================ */

$composerAutoload = $ROOT_DIR . '/vendor/autoload.php';

// Chargement des variables d'environnement depuis le fichier .env
if (is_file($composerAutoload)) {
    require $composerAutoload;
    if (function_exists('log_console')) {
        log_console('Composer autoload chargé', 'ok');
    }
} else {
    if (function_exists('log_console')) {
        log_console('Composer autoload introuvable: ' . $composerAutoload, 'error');
    }
}

$internalConstant = $ROOT_DIR . '/includes/Constant.php';
require_once $internalConstant;
$internalAutoload = $ROOT_DIR . '/includes/Autoloader.php';

if (is_file($internalAutoload)) {
    require_once $internalAutoload;
    if (function_exists('log_console')) {
        log_console('Autoloader interne chargé', 'ok');
    }

    spl_autoload_register([\SAE_CyberCigales_G5\includes\Autoloader::class, 'classLoad']);
    log_console('Autoloader enregistré', 'info');
} else {
    if (function_exists('log_console')) {
        log_console('Autoloader interne introuvable: /includes/Autoloader.php', 'error');
    }
}

/* ============================================================
    Variables d'environnement (.env dans /config)
   ============================================================ */

try {
    if (class_exists(\Dotenv\Dotenv::class)) {
        $dotenv = Dotenv\Dotenv::createImmutable($ROOT_DIR . '/config');
        $dotenv->load();
        if (function_exists('log_console')) {
            log_console('Fichier .env chargé', 'ok');
        }
    } else {
        if (function_exists('log_console')) {
            log_console('Dotenv non disponible (classe introuvable)', 'warn');
        }
    }
} catch (Throwable $e) {
    if (function_exists('log_console')) {
        log_console('Erreur chargement .env (vérifier /config/.env)', 'error');
    }
}

/* ============================================================
    Gestion des erreurs (dev/prod)
    APP_ENV=dev | prod (dev par défaut)
   ============================================================ */

$appEnv = $_ENV['APP_ENV'] ?? 'dev';
if ($appEnv === 'dev') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    if (function_exists('log_console')) {
        log_console('Mode DEV: reporting erreurs activé', 'file');
    }
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    if (function_exists('log_console')) {
        log_console('Mode PROD: reporting erreurs restreint', 'file');
    }
}

/* ============================================================
    Routage et rendu
   ============================================================ */

try {
// Normalisation et nettoyage de l'URI
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $uri = '/' . trim($uri, '/');
    if (function_exists('log_console')) {
        log_console("URI demandée: {$uri}", 'file');
    }

    // Paramètres de route par query string
    $S_controller = $_GET['controller'] ?? 'Redirection';
    $S_action     = $_GET['action'] ?? 'openHomepage';

    // Cache OFF uniquement sur la page d'accueil
    $isHome = ($S_controller === 'Redirection' && $S_action === 'openHomepage');

    if ($isHome) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        log_console("Cache rafraichis depuis la page d'accueil", 'info');
    }

    if (function_exists('log_console')) {
        log_console("Route -> controller={$S_controller}, action={$S_action}", 'file');
    }

    if (isset($_SESSION['utilisateur'])) {
        $progressModel = new GameProgressModel();
        $progress = $progressModel->getByUserId($_SESSION['utilisateur']['id']);

        if ($progress) {
            $_SESSION['team']  = $progress['team'];
            $_SESSION['level'] = (int) $progress['level'];
        } else {
            $_SESSION['level'] = 0;
        }
    }

    // Démarre le buffer de rendu
    if (class_exists(ViewHandler::class)) {
        ViewHandler::bufferStart();
        if (function_exists('log_console')) {
            log_console('Buffer vue démarré', 'ok');
        }
    } else {
        if (function_exists('log_console')) {
            log_console('Classe viewHandler introuvable', 'error');
        }
        throw new RuntimeException('ViewHandler introuvable');
    }

    // Exécute le contrôleur et l'action
    if (class_exists(ControllerHandler::class)) {
        $C_controller = new ControllerHandler($S_controller, $S_action);
        $C_controller->execute();
        if (function_exists('log_console')) {
            log_console('Contrôleur exécuté', 'ok');
        }
    } else {
        if (function_exists('log_console')) {
            log_console('Classe controllerHandler introuvable', 'error');
        }
        throw new RuntimeException('controllerHandler introuvable');
    }

    // Récupère le contenu tamponné
    $displayContent = ViewHandler::bufferCollect();
// Paramètres potentiels exposés par le handler
    $A_params = method_exists($C_controller, 'getParams') ? $C_controller->getParams() : [];
    if (!empty($A_params)) {
        if (function_exists('log_console')) {
            log_console('Params contrôleur collectés', 'file');
        }
    }

    // Affiche le contenu
    echo $displayContent;
    if (function_exists('log_console')) {
        log_console('Contenu affiché', 'ok');
    }
} catch (Throwable $e) {
// Gestion d'erreur globale
    http_response_code(500);
    echo "<main><h1>ERREUR FATALE (DÉBOGAGE)</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . " (Ligne: " . $e->getLine() . ")</p>";
    echo "<hr>";
    echo "<h2>Trace complète (Stack Trace)</h2>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></main>";
    if (function_exists('log_console')) {
        log_console('Exception globale capturée', 'error');
    }
}
