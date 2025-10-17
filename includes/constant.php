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
}