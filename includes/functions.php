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

/*  Récupère une ancienne valeur de formulaire stockée en session
    pour la réafficher dans le formulaire. */

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        $val = $_SESSION['old'][$key] ?? $default;

        // Normalisation & nettoyage agressif
        $val = (string)$val;
        $val = strip_tags($val);                 // vire <script>...</script> & toutes balises
        $val = preg_replace('/[\x00-\x1F\x7F]/', '', $val); // caractères de contrôle
        $val = preg_replace('/\s+/', ' ', $val); // espaces multiples
        $val = trim($val);
        $val = mb_substr($val, 0, 120);         // limite défensive

        // Sortie sûre pour un attribut HTML
        return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
    }
}

/* ============================================================
    Utilitaire de log DEV (commentaires HTML)
    Types: ok, error, file, song, info, warn
   ============================================================ */

if (!function_exists('log_console')) {
    function log_console(string $message, string $type = 'info', array $context = []): void
    {
        // map niveau → libellé
        $label = match ($type) {
            'error' => 'ERROR',
            'warn'  => 'WARNING',
            'ok'    => 'SUCCESS',
            'file'  => 'FILE',
            'song'  => 'AUDIO',
            'info'  => 'INFO',
            default => 'D-INFO',
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

function trimAndSortLogFile(string $file, int $maxBytes = 15 * 1024 * 1024): void
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
    fseek($src, max(0, $size - $keepBytes));
    fgets($src); // saute la ligne partielle

    $lines = [];
    while (!feof($src)) {
        $line = fgets($src);
        if ($line !== false) {
            $lines[] = $line;
        }
    }
    fclose($src);

    // Tri par timestamp ISO (deuxième bloc entre crochets)
    usort($lines, function ($a, $b) {
        preg_match('/\[(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})/', $a, $ma);
        preg_match('/\[(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})/', $b, $mb);
        return strcmp($ma[1] ?? '', $mb[1] ?? '');
    });

    file_put_contents($file, implode('', $lines));
}

// ===================
// Rotation différée
// ===================

if (!function_exists('registerLogRotation')) {
    /**
     * Active la rotation automatique des logs à la fin de chaque requête.
     */
    function registerLogRotation(string $logDir, string $logFile): void
    {
        register_shutdown_function(static function () use ($logDir, $logFile) {
            try {
                // Trim du fichier principal (limite 15 Mo avec 80% gardés)
                trimAndSortLogFile($logFile, 15 * 1024 * 1024);

                // Rotation des logs : garder seulement les 7 plus récents
                $logFiles = glob("{$logDir}/app-*.log");
                if ($logFiles !== false && count($logFiles) > 7) {
                    usort($logFiles, static fn($a, $b) => filemtime($a) <=> filemtime($b));
                    $filesToDelete = array_slice($logFiles, 0, count($logFiles) - 7);
                    foreach ($filesToDelete as $oldLog) {
                        @unlink($oldLog);
                    }
                }

                // Confirmation dans le log du jour
                if (function_exists('log_console')) {
                    log_console('Rotation des logs terminée', 'ok', [
                        'file' => $logFile,
                        'size' => filesize($logFile),
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
