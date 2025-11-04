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

// ============================================================
// 🔧 CONFIGURATION DU LOG SERVEUR (AVANT TOUT)
// ============================================================

// Détermine le dossier racine du projet
$ROOT_DIR = dirname(__DIR__);

// Dossier de logs
$logDir = $ROOT_DIR . '/var/log';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0775, true);
}

// Active le log PHP côté serveur
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/app.log');

/* ============================================================
    Utilitaire de log DEV (commentaires HTML)
    Types: ok, error, file, song, info
   ============================================================ */

if (!function_exists('log_console')) {
    function log_console(string $message, string $type = 'info', array $context = []): void
    {
        // map niveau → libellé
        $label = match ($type) {
            'error' => 'ERROR',
            'warn'  => 'WARNING',
            default => 'INFO',
        };
    // Ajoute un ID de requête pour suivre un flux
        if (!isset($GLOBALS['req_id'])) {
            try {
                $GLOBALS['req_id'] = bin2hex(random_bytes(4));
            } catch (\Throwable $e) {
                $GLOBALS['req_id'] = '00000000';
            }
        }

        // Nettoie le contexte
        unset($context['pwd'], $context['password'], $context['confirm_pwd'], $context['token'], $context['code']);
        $ctx = $context ? (' ' . json_encode($context, JSON_UNESCAPED_UNICODE)) : '';
    // [date] [LEVEL] [req:abcd1234] message {ctx}
        $line = sprintf('[%s] [%s] [req:%s] %s%s', date('c'), $label, $GLOBALS['req_id'], $message, $ctx);
    // écrit côté serveur (AlwaysData ou app.log selon ini_set plus haut)
        error_log($line);
    }

}

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
    log_console('Session démarrée', 'ok');
// ✅
}

set_error_handler(function ($severity, $message, $file, $line) {

    // Ignore les erreurs masquées par @
    if (!(error_reporting() & $severity)) {
        return false;
    }
    log_console("$message @ $file:$line", 'warn');
    return false;
// laisse PHP continuer si besoin
});
set_exception_handler(function (Throwable $e) {

    log_console($e->getMessage() . " @ {$e->getFile()}:{$e->getLine()}", 'error', [
        'trace' => substr($e->getTraceAsString(), 0, 2000),
    ]);
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

$composerAutoload = $ROOT_DIR . '/vendor/Autoload.php';

// Chargement des variables d'environnement depuis le fichier .env
if (is_file($composerAutoload)) {
    require $composerAutoload;
    log_console('Composer autoload chargé', 'ok');
// ✅
} else {
    log_console('Composer autoload introuvable: /vendor/autoload.php', 'error');
// ❌
}

$internalConstant = $ROOT_DIR . '/includes/Constant.php';
require_once $internalConstant;
$internalAutoload = $ROOT_DIR . '/includes/Autoloader.php';

if (is_file($internalAutoload)) {
    require_once $internalAutoload;
    log_console('Autoloader interne chargé', 'ok');
// ✅
} else {
    log_console('Autoloader interne introuvable: /includes/Autoloader.php', 'error'); // ❌
}

/* ============================================================
    Variables d'environnement (.env dans /config)
   ============================================================ */

try {
    if (class_exists(\Dotenv\Dotenv::class)) {
        $dotenv = Dotenv\Dotenv::createImmutable($ROOT_DIR . '/config');
        $dotenv->load();
        log_console('Fichier .env chargé', 'ok');
    // ✅
    } else {
        log_console('Dotenv non disponible (classe introuvable)', 'file');
    // 📄
    }
} catch (Throwable $e) {
    log_console('Erreur chargement .env (vérifier /config/.env)', 'error');
// ❌
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
    log_console('Mode DEV: reporting erreurs activé', 'info');
// ℹ️
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    log_console('Mode PROD: reporting erreurs restreint', 'file');
// 📄
}

/* ============================================================
    Routage et rendu
   ============================================================ */

try {
// Normalisation et nettoyage de l'URI
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $uri = '/' . trim($uri, '/');
    log_console("URI demandée: {$uri}", 'file');// 📄

    // Paramètres de route par query string
    $S_controller = $_GET['controller'] ?? 'Redirection';
    $S_action     = $_GET['action'] ?? 'openHomepage';
    log_console("Route -> controller={$S_controller}, action={$S_action}", 'file');// 📄


    // Démarre le buffer de rendu
    if (class_exists(ViewHandler::class)) {
        ViewHandler::bufferStart();
        log_console('Buffer vue démarré', 'ok');// ✅
    } else {
        log_console('Classe viewHandler introuvable', 'error');// ❌
        throw new RuntimeException('ViewHandler introuvable');
    }

    // Exécute le contrôleur et l'action
    if (class_exists(ControllerHandler::class)) {
        $C_controller = new ControllerHandler($S_controller, $S_action);
        $C_controller->execute();
        log_console('Contrôleur exécuté', 'ok');// ✅
    } else {
        log_console('Classe controllerHandler introuvable', 'error');//❌
        throw new RuntimeException('controllerHandler introuvable');
    }

    // Récupère le contenu tamponné
    $displayContent = ViewHandler::bufferCollect();
// Paramètres potentiels exposés par le handler
    $A_params = method_exists($C_controller, 'getParams') ? $C_controller->getParams() : [];
    if (!empty($A_params)) {
        log_console('Params contrôleur collectés', 'file');
    // 📄
    }

    // Affiche le contenu
    echo $displayContent;
    log_console('Contenu affiché', 'ok');
// ✅
} catch (Throwable $e) {
// Gestion d'erreur globale
    http_response_code(500);

    // Laissez le mode débogage actif pour l'instant si vous voulez
    echo "<main><h1>ERREUR FATALE (DÉBOGAGE)</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . " (Ligne: " . $e->getLine() . ")</p>";
    echo "<hr>";
    echo "<h2>Trace complète (Stack Trace)</h2>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></main>";

    log_console('Exception globale capturée', 'error'); // ❌
}
