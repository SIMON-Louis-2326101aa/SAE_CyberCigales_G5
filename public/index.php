<?php
declare(strict_types=1);
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

/* ============================================================
    Utilitaire de log DEV (commentaires HTML)
    Types: ok, error, file, song, info
   ============================================================ */

if (!function_exists('log_console')) {
    function log_console($message, $type = 'info') {
        $colors = [
            'ok' => 'lime',
            'error' => 'red',
            'warn' => 'orange',
            'info' => 'cyan',
            'file' => 'violet',
        ];
        $icon = [
            'ok' => '✅',
            'error' => '❌',
            'warn' => '⚠️',
            'info' => 'ℹ️',
            'file' => '📄',
        ];
        $color = $colors[$type] ?? 'white';
        $emoji = $icon[$type] ?? '📄';
        echo "<script>console.log('%c{$emoji} [LOG DEV Page] " . addslashes($message) . "', 'color: {$color};');</script>";
    }
}

/* ============================================================
   Chemins racine
   ============================================================ */

$ROOT_DIR = dirname(__DIR__);
log_console("ROOT_DIR={$ROOT_DIR}", 'info'); // ℹ️

/* ============================================================
    Chargements initiaux
   ============================================================ */

$composerAutoload = $ROOT_DIR . '/vendor/autoload.php';

if (is_file($composerAutoload)) {
    require $composerAutoload;
    log_console('Composer autoload chargé', 'ok'); // ✅
} else {
    log_console('Composer autoload introuvable: /vendor/autoload.php', 'error'); // ❌
}

$internalAutoload = $ROOT_DIR . '/includes/autoloader.php';

if (is_file($internalAutoload)) {
    require_once $internalAutoload;
    log_console('Autoloader interne chargé', 'ok'); // ✅
} else {
    log_console('Autoloader interne introuvable: /includes/autoloader.php', 'error'); // ❌
}

/* ============================================================
    Variables d'environnement (.env dans /config)
   ============================================================ */

try {
    if (class_exists(\Dotenv\Dotenv::class)) {
        $dotenv = Dotenv\Dotenv::createImmutable($ROOT_DIR . '/config');
        $dotenv->load();
        log_console('Fichier .env chargé', 'ok'); // ✅
    } else {
        log_console('Dotenv non disponible (classe introuvable)', 'file'); // 📄
    }
} catch (Throwable $e) {
    log_console('Erreur chargement .env (vérifier /config/.env)', 'error'); // ❌
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
    log_console('Mode DEV: reporting erreurs activé', 'info'); // ℹ️
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    log_console('Mode PROD: reporting erreurs restreint', 'file'); // 📄
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
    log_console('Session démarrée', 'ok'); // ✅
}

/* ============================================================
    Routage et rendu
   ============================================================ */

try {
    // Normalisation de l'URI (utile si évolution vers un router par path)
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $uri = '/' . trim($uri, '/');
    log_console("URI demandée: {$uri}", 'file'); // 📄

    // Paramètres de route par query string
    $S_controller = $_GET['controller'] ?? 'redirection';
    $S_action     = $_GET['action'] ?? 'openHomepage';
    log_console("Route -> controller={$S_controller}, action={$S_action}", 'file'); // 📄

    // Démarre le buffer de rendu
    if (class_exists('viewHandler')) {
        viewHandler::bufferStart();
        log_console('Buffer vue démarré', 'ok'); // ✅
    } else {
        log_console('Classe viewHandler introuvable', 'error'); // ❌
        throw new RuntimeException('viewHandler introuvable');
    }

    // Exécute le contrôleur et l'action
    if (class_exists('controllerHandler')) {
        $C_controller = new controllerHandler($S_controller, $S_action);
        $C_controller->execute();
        log_console('Contrôleur exécuté', 'ok'); // ✅
    } else {
        log_console('Classe controllerHandler introuvable', 'error'); //❌
        throw new RuntimeException('controllerHandler introuvable');
    }

    // Récupère le contenu tamponné
    $displayContent = viewHandler::bufferCollect();

    // Paramètres potentiels exposés par le handler
    $A_params = method_exists($C_controller, 'getParams') ? $C_controller->getParams() : [];
    if (!empty($A_params)) {
        log_console('Params contrôleur collectés', 'file'); // 📄
    }

    // Affiche le contenu
    echo $displayContent;
    log_console('Contenu affiché', 'ok'); // ✅

} catch (Throwable $e) {
    // Gestion d'erreur globale
    http_response_code(500);
    echo "<main><h1>Erreur interne</h1><p>Une erreur est survenue.</p></main>";
    log_console('Exception globale capturée', 'error'); // ❌
}
