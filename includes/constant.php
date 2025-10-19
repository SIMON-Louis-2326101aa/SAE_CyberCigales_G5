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

    // Indique si l'on est en environnement de développement/local
    public static function isDev(): bool
    {
        $server = $_SERVER['SERVER_NAME'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        
        // Environnements de développement
        $devEnvironments = ['127.0.0.1', 'localhost', 'dev', 'development'];
        
        // Vérifier le serveur et l'host
        if (in_array($server, $devEnvironments) || in_array($host, $devEnvironments)) {
            return true;
        }
        
        // Vérifier si on contient "localhost" ou "127.0.0.1" dans l'URL
        if (strpos($server, 'localhost') !== false || strpos($host, 'localhost') !== false) {
            return true;
        }
        
        // Vérifier l'IP locale
        if (strpos($server, '127.0.0.1') !== false || strpos($host, '127.0.0.1') !== false) {
            return true;
        }
        
        // Par défaut, considérer comme production
        return false;
    }
}