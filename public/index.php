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

date_default_timezone_set('Europe/Paris');

// ============================================================
//  CONFIGURATION DU LOG SERVEUR
// ============================================================

// Détermine le dossier racine du projet
$ROOT_DIR = dirname(__DIR__);

require_once $ROOT_DIR . '/includes/functions.php';

// Pré-charge APP_ENV avant les premiers logs bootstrap
$earlyEnvFile = $ROOT_DIR . '/config/.env';

if (!isset($_ENV['APP_ENV']) && is_file($earlyEnvFile)) {
    $envLines = file($earlyEnvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($envLines !== false) {
        foreach ($envLines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'APP_ENV=')) {
                $value = trim(substr($line, strlen('APP_ENV=')));
                $value = trim($value, " \t\n\r\0\x0B\"'");

                $_ENV['APP_ENV'] = $value;
                putenv("APP_ENV={$value}");
                break;
            }
        }
    }
}

//  Timer de requête pour mesurer la durée totale du traitement
$REQUEST_START = microtime(true);

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
ini_set('log_errors', '1');
ini_set('error_log', $logFile);

registerLogRotation($logDir, $logFile);

/* ============================================================
    Chargements initiaux
   ============================================================ */

$composerAutoload = $ROOT_DIR . '/vendor/autoload.php';

if (is_file($composerAutoload)) {
    require $composerAutoload;
    if (function_exists('log_console')) {
        log_console('Composer autoload chargé', 'file');
    }
} else {
    if (function_exists('log_console')) {
        log_console('Composer autoload introuvable', 'error');
    }
}

$internalConstant = $ROOT_DIR . '/includes/Constant.php';
require_once $internalConstant;
$internalAutoload = $ROOT_DIR . '/includes/Autoloader.php';

if (is_file($internalAutoload)) {
    require_once $internalAutoload;
    if (function_exists('log_console')) {
        log_console('Autoloader interne chargé', 'file');
    }

    spl_autoload_register([\SAE_CyberCigales_G5\includes\Autoloader::class, 'classLoad']);
    log_console('Autoloader enregistré', 'file');
} else {
    if (function_exists('log_console')) {
        log_console('Autoloader interne introuvable', 'error');
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
            log_console('Configuration environnement chargée', 'file');
        }
    } else {
        if (function_exists('log_console')) {
            log_console('Dotenv non disponible', 'warn');
        }
    }
} catch (Throwable $e) {
    if (function_exists('log_console')) {
        log_console('Erreur chargement configuration environnement', 'error');
    }
}

/* ============================================================
    CONFIGURATION AUTOMATIQUE SELON APP_ENV
   ============================================================ */

$appEnv = $_ENV['APP_ENV'] ?? 'dev';

switch ($appEnv) {
    case 'prod':
        $_ENV['APP_DEBUG'] = '0';
        $_ENV['LOG_MODE'] = 'public';
        $_ENV['LOG_LEVEL'] = 'warn';
        $_ENV['LOG_CONTEXT_ENABLED'] = '0';
        $_ENV['LOG_SECURITY_ENABLED'] = '1';
        $_ENV['FORCE_SECURE'] = '1';
        break;

    case 'dev':
    default:
        $_ENV['APP_DEBUG'] = '1';
        $_ENV['LOG_MODE'] = 'private';
        $_ENV['LOG_LEVEL'] = 'debug';
        $_ENV['LOG_CONTEXT_ENABLED'] = '1';
        $_ENV['LOG_SECURITY_ENABLED'] = '1';
        $_ENV['FORCE_SECURE'] = '0';
        break;
}

if (function_exists('log_console')) {
    log_console('Configuration environnement appliquée', 'file', [
        'APP_ENV' => $appEnv,
        'LOG_MODE' => $_ENV['LOG_MODE'],
        'LOG_LEVEL' => $_ENV['LOG_LEVEL']
    ]);
}

/* ============================================================
    Session sécurisée
   ============================================================ */

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
        log_console('Session démarrée', 'file', [
            'cookie_secure' => $cookieSecure,
            'cookie_samesite' => $cookieSameSite,
            'https_detected' => $isHttps,
        ]);
    }
}

set_error_handler(function ($severity, $message, $file, $line) {

    // Ignore les erreurs masquées par @
    if (!(error_reporting() & $severity)) {
        return false;
    }
    if (function_exists('log_console')) {
        log_console("$message @ $file:$line", 'warn', [
            'severity' => $severity,
        ]);
    }
    return false;
});

set_exception_handler(function (Throwable $e) {
    if (function_exists('log_console')) {
        log_console($e->getMessage() . " @ {$e->getFile()}:{$e->getLine()}", 'error', [
            'trace' => substr($e->getTraceAsString(), 0, 2000),
            'uri' => $_SERVER['REQUEST_URI'] ?? null,
            'app_env' => $_ENV['APP_ENV'] ?? 'dev',
        ]);
    }
});

register_shutdown_function(function () {

    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        log_console($e['message'] . " @ {$e['file']}:{$e['line']}", 'error', [
            'type' => $e['type'],
        ]);
    }
});

/* ============================================================
    Gestion des erreurs (dev/prod)
   ============================================================ */

if (($_ENV['APP_DEBUG'] ?? '1') === '1') {
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
        log_console('Route demandée', 'info', [
            'uri' => $uri,
        ]);
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
        log_console('Route résolue', 'info', [
            'controller' => $S_controller,
            'action' => $S_action,
        ]);
    }

    if (isset($_SESSION['utilisateur'])) {
        $progressModel = new GameProgressModel();
        $progress = $progressModel->getByUserId($_SESSION['utilisateur']['id']);

        if ($progress) {
            $_SESSION['team']  = $progress['team'];
            $_SESSION['level'] = (int)$progress['level'];

            if (function_exists('log_console')) {
                log_console('Progression utilisateur restaurée', 'info', [
                    'user_id' => $_SESSION['utilisateur']['id'],
                    'team' => $progress['team'] ?? null,
                    'level' => (int)($progress['level'] ?? 0),
                ]);
            }
        } else {
            $_SESSION['level'] = 0;

            if (function_exists('log_console')) {
                log_console('Aucune progression trouvée pour l’utilisateur connecté', 'info', [
                    'user_id' => $_SESSION['utilisateur']['id'],
                ]);
            }
        }
    }

    // Démarre le buffer de rendu
    if (class_exists(ViewHandler::class)) {
        ViewHandler::bufferStart();
        if (function_exists('log_console')) {
            log_console('Buffer vue démarré', 'file');
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
            log_console('Contrôleur exécuté', 'file');
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
            log_console('Params contrôleur collectés', 'file', [
                'count' => count($A_params),
            ]);
        }
    }

    // Affiche le contenu
    echo $displayContent;
    if (function_exists('log_console')) {
        log_console('Contenu affiché', 'file', [
            'controller' => $S_controller,
            'action' => $S_action,
            'duration_ms' => round((microtime(true) - $REQUEST_START) * 1000, 2),
            'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ]);
    }
} catch (Throwable $e) {
    http_response_code(500);

    // Log serveur toujours actif
    if (function_exists('log_console')) {
        log_console('Exception globale capturée', 'error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

    $isDebug = ($_ENV['APP_DEBUG'] ?? '0') === '1';

    $requestId = $GLOBALS['req_id'] ?? 'unknown';

    if ($isDebug) {
        // ===== MODE DEV =====
        echo "<main><h1>ERREUR FATALE (DEV)</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . " (Ligne: " . $e->getLine() . ")</p>";
        echo "<hr>";
        echo "<h2>Trace complète (Stack Trace)</h2>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></main>";
        echo "<p><strong>ID de l'erreur :</strong> " . htmlspecialchars($requestId) . "</p>";
        echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
    } else {
        // ===== MODE PROD =====
        echo "<main>";
        echo "<h1>Une erreur est survenue.</h1>";
        echo "<p>Le problème a été enregistré.</p>";
        echo "<p>Si le problème persiste, veuillez contacter les développeurs via la section contact.</p>";
        echo "<p><strong>ID de l'erreur :</strong> " . htmlspecialchars($requestId) . "</p>";
        echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
        echo "</main>";
    }
}
