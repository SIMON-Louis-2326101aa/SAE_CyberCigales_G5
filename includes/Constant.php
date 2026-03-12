<?php

/**
 * Constant
 * - Centralise les répertoires clés du projet.
 * - Fournit des helpers pour récupérer les chemins absolus.
 *
 * Remarques :
 * - Les constantes *_DIR conservent les slashes d'origine pour ne rien casser.
 * - Les méthodes *Dir() retournent des chemins absolus normalisés et sans slash final.
 */

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

final class Constant
{
    /** Sous-répertoires (relatifs à la racine du projet) **/
    public const VIEW_DIR       = '/Modules/view/';
    public const MODEL_DIR      = '/Modules/model/';
    public const CONTROLLER_DIR = '/Modules/controller/';
    public const INCLUDES_DIR   = '/includes/';

    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    /**
     * Normalise un chemin :
     * - convertit les backslashes en slashes,
     * - retire le slash final.
     */
    private static function normalize(string $path): string
    {
        $p = str_replace('\\', '/', $path);
        return rtrim($p, '/');
    }

    /**
     * Retourne le chemin racine absolu du projet (cache après premier calcul).
     */
    public static function indexDir(): string
    {
        static $root = null;

        if ($root !== null) {
            return $root;
        }

        // __DIR__ pointe sur /includes ; on remonte d'un cran vers la racine du projet
        $resolved = realpath(__DIR__ . '/../');

        if ($resolved === false) {
            $fallback = self::normalize(__DIR__ . '/..');
            self::log('realpath a échoué, fallback utilisé pour la racine projet', 'warn', [
                'fallback' => $fallback,
            ]);
            $root = $fallback;
            return $root;
        }

        $root = self::normalize($resolved);

        self::log('Racine projet détectée', 'file', [
            'root' => $root,
        ]);

        return $root;
    }

    /**
     * Retourne le chemin absolu du répertoire des vues.
     */
    public static function viewDir(): string
    {
        $dir = self::normalize(self::indexDir() . self::VIEW_DIR);
        self::log('Répertoire vues résolu', 'file', [
            'view_dir' => $dir,
        ]);
        return $dir;
    }

    /**
     * Retourne le chemin absolu du répertoire des modèles.
     */
    public static function modelDir(): string
    {
        $dir = self::normalize(self::indexDir() . self::MODEL_DIR);
        self::log('Répertoire modèles résolu', 'file', [
            'model_dir' => $dir,
        ]);
        return $dir;
    }

    /**
     * Retourne le chemin absolu du répertoire des contrôleurs.
     */
    public static function controllerDir(): string
    {
        $dir = self::normalize(self::indexDir() . self::CONTROLLER_DIR);
        self::log('Répertoire contrôleurs résolu', 'file', [
            'controller_dir' => $dir,
        ]);
        return $dir;
    }

    /**
     * Retourne le chemin absolu du répertoire includes.
     */
    public static function includesDir(): string
    {
        $dir = self::normalize(self::indexDir() . self::INCLUDES_DIR);
        self::log('Répertoire includes résolu', 'file', [
            'includes_dir' => $dir,
        ]);
        return $dir;
    }

    public static function isDev(): bool
    {
        // 1) Priorité à APP_ENV
        $env = $_ENV['APP_ENV'] ?? null;
        if ($env === null) {
            $env = getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? null);
        }

        if ($env !== null) {
            $env = strtolower(trim((string)$env));
            $isDev = in_array($env, ['dev', 'development', 'local'], true);

            self::log('Détection environnement via APP_ENV', 'file', [
                'app_env' => $env,
                'is_dev' => $isDev,
            ]);

            return $isDev;
        }

        // 2) Fallback sur host/serveur (utile si on n'utilise pas de .env en local)
        $server = strtolower($_SERVER['SERVER_NAME'] ?? '');
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
        $devEnvironments = ['127.0.0.1', 'localhost', '::1'];

        foreach ($devEnvironments as $dev) {
            if ($dev !== '' && (strpos($server, $dev) !== false || strpos($host, $dev) !== false)) {
                self::log('Détection environnement via host/serveur', 'file', [
                    'server_name' => $server,
                    'http_host' => $host,
                    'matched' => $dev,
                    'is_dev' => true,
                ]);
                return true;
            }
        }

        self::log('Aucun environnement dev détecté, mode production supposé', 'file', [
            'server_name' => $server,
            'http_host' => $host,
            'is_dev' => false,
        ]);

        return false;
    }
}
