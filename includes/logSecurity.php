<?php

/**
 * logSecurity.php
 * Utilitaires de journalisation sécurité.
 * - Écrit chaque événement sur une ligne (format JSONL) avec horodatage.
 * - Ajoute un contexte utile: IP, user agent, URL, méthode, user session, etc.
 * - Masque les données sensibles (ex: password, token).
 * - Rotation simple du fichier si trop volumineux.
 * - En dev, trace aussi via log_dev().
 */

declare(strict_types=1);

if (!function_exists('logSecurityEvent')) {
    /**
     * Journalise un événement de sécurité.
     * @param string $type     Catégorie (ex: "sql_injection", "xss_attempt", "csrf_fail", "login_fail")
     * @param string $message  Description courte de l'événement
     * @param array  $context  Contexte additionnel non sensible (ex: ['param' => 'id'])
     */
    function logSecurityEvent(string $type, string $message, array $context = []): void
    {
        // Détermine chemins de stockage
        $rootDir = dirname(__DIR__);
        $logDir  = $rootDir . '/storage/logs';
        $logFile = $logDir . '/security_events.log';

        // Crée le dossier si nécessaire
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        // Rotation simple si fichier > 5 MB
        if (is_file($logFile) && filesize($logFile) > 5 * 1024 * 1024) {
            $rotated = $logFile . '.' . date('Ymd_His');
            @rename($logFile, $rotated);
        }

        // Construit le contexte de requête
        $request = [
            'time'         => date('c'), // ISO 8601 (inclut date + heure + timezone)
            'type'         => $type,
            'message'      => $message,
            'ip'           => $_SERVER['REMOTE_ADDR']        ?? null,
            'method'       => $_SERVER['REQUEST_METHOD']     ?? null,
            'url'          => (($_SERVER['HTTPS'] ?? 'off') !== 'off' ? 'https://' : 'http://')
                . ($_SERVER['HTTP_HOST'] ?? 'localhost')
                . ($_SERVER['REQUEST_URI'] ?? '/'),
            'referer'      => $_SERVER['HTTP_REFERER']       ?? null,
            'user_agent'   => $_SERVER['HTTP_USER_AGENT']    ?? null,
            'session_user' => $_SESSION['user_id']           ?? null, // adapte la clé selon ton app
            'app_env'      => $_ENV['APP_ENV']              ?? null,
            'is_dev'       => (($_ENV['APP_ENV'] ?? 'dev') === 'dev'),
        ];

        // Ajoute un petit identifiant de corrélation pour relier plusieurs logs
        $request['cid'] = substr(sha1(
            ($request['ip'] ?? '-') . '|' . microtime(true) . '|' . random_int(0, PHP_INT_MAX)
        ), 0, 12);

        // Concatène le contexte passé par l'appelant
        $payload = array_merge($request, [
            'context' => maskSensitive($context + collectRequestParams()),
        ]);

        // Encode en JSON sur une ligne
        $line = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        // Écrit avec verrouillage
        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

        // Log dev discret
        if (function_exists('log_console')) {
            // On reste neutre côté prod si tu as codé log_dev() pour minimiser le HTML
            $severity = in_array($type, ['sql_injection', 'xss_attempt', 'csrf_fail'], true) ? 'error' : 'info';
            log_console("Security event '{$type}' enregistré", $severity);
        }
    }
}

/**
 * Récupère les paramètres utiles de la requête (GET/POST) en masquant les sensibles.
 */
if (!function_exists('collectRequestParams')) {
    function collectRequestParams(): array
    {
        $data = [
            'get'  => $_GET  ?? [],
            'post' => $_POST ?? [],
        ];
        return maskSensitive($data);
    }
}

/**
 * Masque des clés sensibles dans un tableau assoc (récursif).
 * Ex: password, pass, pwd, token, authorization, csrf, secret.
 */
if (!function_exists('maskSensitive')) {
    function maskSensitive(array $input): array
    {
        $sensitiveKeys = [
            'password', 'pass', 'pwd',
            'token', 'access_token', 'refresh_token',
            'authorization', 'auth', 'csrf', 'xsrf',
            'secret', 'apikey', 'api_key',
        ];

        $out = [];
        foreach ($input as $k => $v) {
            $lower = is_string($k) ? strtolower($k) : $k;

            if (is_array($v)) {
                $out[$k] = maskSensitive($v);
            } else {
                if (in_array($lower, $sensitiveKeys, true)) {
                    $out[$k] = '***MASKED***';
                } else {
                    // Limite la taille pour éviter d'énormes dumps
                    $out[$k] = (is_string($v) && strlen($v) > 500) ? substr($v, 0, 500) . '…' : $v;
                }
            }
        }
        return $out;
    }
}
