<?php
final class Constant
{
    const VIEW_DIR = '/Modules/view/';
    const MODEL_DIR = '/Modules/model/';
    const CONTROLLER_DIR = '/Modules/controller/';
    const INCLUDES_DIR = '/includes/';

    // Retourne le chemin racine du projet
    public static function indexDir(): string
    {
        return realpath(__DIR__ . '/../');
    }

    // Retourne le chemin complet vers le répertoire des vues
    public static function viewDir(): string
    {
        return self::indexDir() . self::VIEW_DIR;
    }

    // Retourne le chemin complet vers le répertoire des modèles
    public static function modelDir(): string
    {
        return self::indexDir() . self::MODEL_DIR;
    }

    // Retourne le chemin complet vers le répertoire des contrôleurs
    public static function controllerDir(): string
    {
        return self::indexDir() . self::CONTROLLER_DIR;
    }
    public static function includesDir(): string
    {
        return self::indexDir() . self::INCLUDES_DIR;
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
            return in_array($env, ['dev', 'development', 'local'], true);
        }

        // 2) Fallback sur host/serveur (utile si on n'utilise pas de .env en local)
        $server = strtolower($_SERVER['SERVER_NAME'] ?? '');
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
        $devEnvironments = ['127.0.0.1', 'localhost', '::1'];

        foreach ($devEnvironments as $dev) {
            if ($dev !== '' && (strpos($server, $dev) !== false || strpos($host, $dev) !== false)) {
                return true;
            }
        }

        // Par défaut, considérer comme production
        return false;
    }

}