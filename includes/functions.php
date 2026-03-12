<?php

/**
 * Récupère une ancienne valeur de formulaire stockée en session.
 * - Nettoie et normalise la valeur pour un usage sûr dans un attribut HTML.
 *
 * @param string $key     Clé de la valeur à récupérer.
 * @param string $default Valeur par défaut si la clé n'existe pas.
 * @return string Valeur nettoyée et sécurisée.
 */

declare(strict_types=1);

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        $val = $_SESSION['old'][$key] ?? $default;

        $val = (string)$val;
        $val = strip_tags($val);
        $val = preg_replace('/[\x00-\x1F\x7F]/', '', $val);
        $val = preg_replace('/\s+/', ' ', $val);
        $val = trim($val);
        $val = mb_substr($val, 0, 120);

        return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
    }
}

/* ============================================================
   Helpers logs
   ============================================================ */

if (!function_exists('logTypeToNumericLevel')) {
    function logTypeToNumericLevel(string $type): int
    {
        return match ($type) {
            'error' => 40,
            'warn'  => 30,
            'ok', 'info', 'file', 'song' => 20,
            default => 10,
        };
    }
}

if (!function_exists('logEnvMinLevel')) {
    function logEnvMinLevel(): int
    {
        $level = strtolower($_ENV['LOG_LEVEL'] ?? 'debug');

        return match ($level) {
            'error' => 40,
            'warn'  => 30,
            'info'  => 20,
            'debug' => 10,
            default => 10,
        };
    }
}

if (!function_exists('sanitizeLogContext')) {
    function sanitizeLogContext(array $context): array
    {
        $sensitiveKeys = [
            'pwd', 'password', 'confirm_pwd', 'token', 'code',
            'access_token', 'refresh_token', 'authorization',
            'secret', 'api_key', 'apikey', 'csrf', 'xsrf'
        ];

        $clean = [];

        foreach ($context as $key => $value) {
            $lowerKey = is_string($key) ? strtolower($key) : $key;

            if (is_array($value)) {
                $clean[$key] = sanitizeLogContext($value);
                continue;
            }

            if (in_array($lowerKey, $sensitiveKeys, true)) {
                $clean[$key] = '***MASKED***';
                continue;
            }

            if (is_string($value)) {
                $clean[$key] = mb_substr($value, 0, 500);
            } else {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }
}

if (!function_exists('filterLogContextByMode')) {
    function filterLogContextByMode(array $context): array
    {
        $mode = $_ENV['LOG_MODE'] ?? 'private';
        $contextEnabled = ($_ENV['LOG_CONTEXT_ENABLED'] ?? '1') === '1';

        if (!$contextEnabled) {
            return [];
        }

        $context = sanitizeLogContext($context);

        if ($mode === 'public') {
            $allowedKeys = [
                'controller',
                'action',
                'uri',
                'user_id',
                'team',
                'level',
                'duration_ms',
                'memory_peak_mb',
                'count',
                'severity',
                'type',
                'APP_ENV',
                'LOG_MODE',
                'LOG_LEVEL',
                'cookie_secure',
                'cookie_samesite',
                'https_detected',
                'file',
                'size',
                'env_path'
            ];

            $filtered = [];

            foreach ($context as $key => $value) {
                if (in_array((string)$key, $allowedKeys, true)) {
                    $filtered[$key] = $value;
                }
            }

            return $filtered;
        }

        return $context;
    }
}

/* ============================================================
   Utilitaire de log APP
   ============================================================ */

if (!function_exists('log_console')) {
    function log_console(string $message, string $type = 'info', array $context = []): void
    {
        $label = match ($type) {
            'error' => 'ERROR',
            'warn'  => 'WARNING',
            'ok'    => 'SUCCESS',
            'file'  => 'FILE',
            'song'  => 'AUDIO',
            'info'  => 'INFO',
            default => 'D-INFO',
        };

        if (!isset($GLOBALS['req_id'])) {
            try {
                $GLOBALS['req_id'] = bin2hex(random_bytes(4));
            } catch (\Throwable $e) {
                $GLOBALS['req_id'] = '00000000';
            }
        }


        $appEnv = $_ENV['APP_ENV']
            ?? getenv('APP_ENV')
            ?? $_SERVER['APP_ENV']
            ?? 'prod';

        // Filtrage prod
        if ($appEnv === 'prod') {
            if (in_array($type, ['file', 'ok', 'song'], true)) {
                return;
            }
        }

        unset(
            $context['pwd'],
            $context['password'],
            $context['confirm_pwd'],
            $context['token'],
            $context['code']
        );

        $ctx = $context ? (' ' . json_encode($context, JSON_UNESCAPED_UNICODE)) : '';

        $line = sprintf('[req:%s] [%s] %s%s', $GLOBALS['req_id'], $label, $message, $ctx);
        error_log($line);
    }
}

/* ============================================================
   Trim + tri fichier logs
   ============================================================ */

if (!function_exists('trimAndSortLogFile')) {

    function trimAndSortLogFile(string $file, int $maxBytes = 20 * 1024 * 1024): void
    {

        if (!is_file($file)) {
            return;
        }

        $size = filesize($file);

        if ($size <= $maxBytes) {
            return;
        }

        $keepBytes = (int) floor($maxBytes * 0.8);

        $src = fopen($file, 'rb');

        if ($src === false) {
            error_log('[LOG-TRIM ERROR] Impossible d’ouvrir le fichier de log.');
            return;
        }

        fseek($src, max(0, $size - $keepBytes));
        fgets($src);

        $lines = [];

        while (!feof($src)) {
            $line = fgets($src);

            if ($line !== false) {
                $lines[] = $line;
            }
        }

        fclose($src);

        usort($lines, function ($a, $b) {

            preg_match('/\[(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})/', $a, $ma);
            preg_match('/\[(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})/', $b, $mb);

            return strcmp($ma[1] ?? '', $mb[1] ?? '');
        });

        file_put_contents($file, implode('', $lines));

        if (function_exists('log_console')) {
            log_console('Trim du fichier de logs effectué', 'warn', [
                'file' => $file,
                'old_size_bytes' => $size,
                'kept_bytes' => $keepBytes,
                'remaining_lines' => count($lines),
            ]);
        }
    }
}

/* ============================================================
   Rotation différée
   ============================================================ */

if (!function_exists('registerLogRotation')) {

    function registerLogRotation(string $logDir, string $logFile): void
    {

        register_shutdown_function(static function () use ($logDir, $logFile) {

            try {
                trimAndSortLogFile($logFile, 20 * 1024 * 1024);

                $logFiles = glob("{$logDir}/app-*.log");
                $deletedCount = 0;

                if ($logFiles !== false && count($logFiles) > 7) {
                    usort($logFiles, static fn($a, $b) => filemtime($a) <=> filemtime($b));

                    $filesToDelete = array_slice(
                        $logFiles,
                        0,
                        count($logFiles) - 7
                    );

                    foreach ($filesToDelete as $oldLog) {
                        if (@unlink($oldLog)) {
                            $deletedCount++;
                        }
                    }
                }

                if (function_exists('log_console')) {
                    log_console('Rotation des logs terminée', 'file', [
                        'file' => $logFile,
                        'size' => is_file($logFile) ? filesize($logFile) : 0,
                        'deleted_files' => $deletedCount,
                    ]);
                }
            } catch (Throwable $e) {
                if (function_exists('log_console')) {
                    log_console('[LOG-ROTATE ERROR] ' . $e->getMessage(), 'error');
                } else {
                    error_log('[LOG-ROTATE ERROR] ' . $e->getMessage());
                }
            }
        });
    }
}
