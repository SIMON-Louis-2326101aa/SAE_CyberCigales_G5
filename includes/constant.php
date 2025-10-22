<?php
/**
 * Classe de constantes pour les chemins du projet
 * 
 * Cette classe centralise tous les chemins importants du projet (MVC).
 * Elle permet d'avoir des chemins cohérents dans toute l'application.
 * 
 * Utilisation :
 * - Constant::indexDir() retourne le chemin racine du projet
 * - Constant::viewDir() retourne le chemin vers les vues
 * - Constant::modelDir() retourne le chemin vers les modèles
 * - etc.
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

final class Constant
{
    /**
     * Constantes de chemins relatifs (depuis la racine du projet)
     */
    const VIEW_DIR = '/Modules/view/';
    const MODEL_DIR = '/Modules/model/';
    const CONTROLLER_DIR = '/Modules/controller/';
    const INCLUDES_DIR = '/includes/';

    /**
     * Retourne le chemin absolu vers la racine du projet
     * 
     * @return string Chemin absolu vers la racine
     */
    public static function indexDir(): string
    {
        return realpath(__DIR__ . '/../');
    }

    /**
     * Retourne le chemin complet vers le répertoire des vues
     * 
     * @return string Chemin absolu vers Modules/view/
     */
    public static function viewDir(): string
    {
        return self::indexDir() . self::VIEW_DIR;
    }

    /**
     * Retourne le chemin complet vers le répertoire des modèles
     * 
     * @return string Chemin absolu vers Modules/model/
     */
    public static function modelDir(): string
    {
        return self::indexDir() . self::MODEL_DIR;
    }

    /**
     * Retourne le chemin complet vers le répertoire des contrôleurs
     * 
     * @return string Chemin absolu vers Modules/controller/
     */
    public static function controllerDir(): string
    {
        return self::indexDir() . self::CONTROLLER_DIR;
    }
    
    /**
     * Retourne le chemin complet vers le répertoire includes
     * 
     * @return string Chemin absolu vers includes/
     */
    public static function includesDir(): string
    {
        return self::indexDir() . self::INCLUDES_DIR;
    }
}