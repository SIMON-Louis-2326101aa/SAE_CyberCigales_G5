<?php

/**
 * logSecurity.php
 * Utilitaires de journalisation sécurité.
 */

declare(strict_types=1);

/**
 * Charge functions.php si nécessaire.
 */
if (!function_exists('ensureSecurityHelpersLoaded')) {
    function ensureSecurityHelpersLoaded(): void
    {
        if (!function_exists('log_console')) {
            $rootDir = dirname(__DIR__);
            $functionsPath = $rootDir . '/includes/functions.php';

            if (is_file($functionsPath)) {
                require_once $functionsPath;
            }
        }
    }
}

if (!function_exists('isSecurityLoggingEnabled')) {
    function isSecurityLoggingEnabled(): bool
    {
        return ($_ENV['LOG_SECURITY_ENABLED'] ?? '1') === '1';
    }
}

if (!function_exists('buildSecurityCorrelationId')) {
    function buildSecurityCorrelationId(?string $ip): string
    {
        try {
            return substr(
                sha1(($ip ?? '-') . '|' . microtime(true) . '|' . random_int(0, PHP_INT_MAX)),
                0,
                12
            );
        } catch (Throwable) {
            return substr(sha1(($ip ?? '-') . '|' . microtime(true)), 0, 12);
        }
    }
}

if (!function_exists('maskSensitiveSecurityData')) {
    function maskSensitiveSecurityData(array $input): array
    {
        $sensitiveKeys = [
            'password', 'pass', 'pwd',
            'token', 'access_token', 'refresh_token',
            'authorization', 'auth', 'csrf', 'xsrf',
            'secret', 'apikey', 'api_key',
            'code', 'confirm_pwd', 'confirm_password'
        ];

        $out = [];

        foreach ($input as $k => $v) {
            $lower = is_string($k) ? strtolower($k) : $k;

            if (is_array($v)) {
                $out[$k] = maskSensitiveSecurityData($v);
                continue;
            }

            if (in_array($lower, $sensitiveKeys, true)) {
                $out[$k] = '***MASKED***';
                continue;
            }

            $out[$k] = (is_string($v) && strlen($v) > 500)
                ? substr($v, 0, 500) . '…'
                : $v;
        }

        return $out;
    }
}

if (!function_exists('collectSecurityRequestParams')) {
    function collectSecurityRequestParams(): array
    {
        $data = [
            'get'  => $_GET ?? [],
            'post' => $_POST ?? [],
        ];

        return maskSensitiveSecurityData($data);
    }
}

if (!function_exists('filterSecurityContextByMode')) {
    function filterSecurityContextByMode(array $context): array
    {
        $mode = $_ENV['LOG_MODE'] ?? 'private';
        $contextEnabled = ($_ENV['LOG_CONTEXT_ENABLED'] ?? '1') === '1';

        if (!$contextEnabled) {
            return [];
        }

        $context = maskSensitiveSecurityData($context);

        if ($mode === 'public') {
            $allowedKeys = [
                'time',
                'type',
                'message',
                'ip',
                'method',
                'url',
                'referer',
                'session_user',
                'app_env',
                'is_dev',
                'cid',
                'context',
            ];

            $filtered = [];

            foreach ($context as $key => $value) {
                if (in_array((string)$key, $allowedKeys, true)) {
                    $filtered[$key] = $value;
                }
            }

            unset($filtered['user_agent']);

            return $filtered;
        }

        return $context;
    }
}

if (!function_exists('rotateSecurityLogIfNeeded')) {
    function rotateSecurityLogIfNeeded(string $logFile): void
    {
        if (is_file($logFile) && filesize($logFile) > 5 * 1024 * 1024) {
            $rotated = $logFile . '.' . date('Ymd_His');
            @rename($logFile, $rotated);

            ensureSecurityHelpersLoaded();

            if (function_exists('log_console')) {
                log_console('Rotation log sécurité effectuée', 'warn', [
                    'file' => $logFile,
                    'rotated_to' => $rotated,
                ]);
            }
        }
    }
}

if (!function_exists('logSecurityEvent')) {
    function logSecurityEvent(string $type, string $message, array $context = []): void
    {
        if (!isSecurityLoggingEnabled()) {
            return;
        }

        ensureSecurityHelpersLoaded();

        $rootDir = dirname(__DIR__);
        $logDir  = $rootDir . '/storage/logs';
        $logFile = $logDir . '/security_events.log';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        rotateSecurityLogIfNeeded($logFile);

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $request = [
            'time'         => date('c'),
            'type'         => $type,
            'message'      => $message,
            'ip'           => $ip,
            'method'       => $_SERVER['REQUEST_METHOD'] ?? null,
            'url'          => (($_SERVER['HTTPS'] ?? 'off') !== 'off' ? 'https://' : 'http://')
                . ($_SERVER['HTTP_HOST'] ?? 'localhost')
                . ($_SERVER['REQUEST_URI'] ?? '/'),
            'referer'      => $_SERVER['HTTP_REFERER'] ?? null,
            'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'session_user' => $_SESSION['user_id'] ?? null,
            'app_env'      => $_ENV['APP_ENV'] ?? null,
            'is_dev'       => (($_ENV['APP_ENV'] ?? 'dev') === 'dev'),
            'cid'          => buildSecurityCorrelationId($ip),
        ];

        $payload = array_merge($request, [
            'context' => maskSensitiveSecurityData($context + collectSecurityRequestParams()),
        ]);

        $payload = filterSecurityContextByMode($payload);

        $line = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        $writeOk = @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

        if ($writeOk === false) {
            error_log('[SECURITY-LOG ERROR] Impossible d’écrire dans security_events.log');
        }

        if (function_exists('log_console')) {
            $severity = match ($type) {
                'sql_injection', 'xss_attempt', 'csrf_fail', 'privilege_escalation', 'path_traversal' => 'error',
                'login_fail', 'forbidden_access', 'suspicious_input' => 'warn',
                default => 'file',
            };

            log_console("SECURITY: {$type}", $severity, [
                'message' => $message,
                'ip' => $ip,
                'url' => $_SERVER['REQUEST_URI'] ?? null,
                'type' => $type,
                'cid' => $payload['cid'] ?? null,
            ]);
        }
    }
}
