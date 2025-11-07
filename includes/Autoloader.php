<?php

/**
 * Autoloader.php
 * - Parcourt les répertoires déclarés dans Constant::*_DIR.
 * - Construit des chemins absolus à partir de Constant::indexDir().
 */

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

final class Autoloader
{
    /** Empêche l'instanciation. */
    private function __construct()
    {
    }

    private static function log(string $message, string $type): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type);
        }
    }
    /**
     * Tente de charger un fichier si lisible.
     * @param string $path Chemin absolu vers le fichier.
     */
    private static function loadFile(string $path): bool
    {
        if (is_readable($path)) {
            require $path;
            self::log("Chargé: {$path}", 'ok');
            return true;
        }
        self::log("Non lisible: {$path}", 'file');
        return false;
    }

    /**
     * Autoload principal.
     *  Charge automatiquement une classe en cherchant dans les différents répertoire
     * Exemple: "controllerHandler" -> <dir>/ControllerHandler.php
     */
    public static function classLoad(string $className): bool
    {
        if (false !== $lastNsPos = strrpos($className, '\\')) {
            $className = substr($className, $lastNsPos + 1);
        }
        // Vérifie la classe Constant (chemins)
        if (!class_exists(Constant::class)) {
            self::log('Classe Constant introuvable (includes/Constant.php non chargé ?)', 'error');
            return false;
        }

        // Racine projet + dossiers à parcourir (sans slash final)
        $root = rtrim(Constant::indexDir(), '/\\');
        $dirs = [
            rtrim(Constant::INCLUDES_DIR, '/\\'),
            rtrim(Constant::MODEL_DIR, '/\\'),
            rtrim(Constant::VIEW_DIR, '/\\'),
            rtrim(Constant::CONTROLLER_DIR, '/\\'),
        ];

        // Type de fichier attendu
        $fileName = $className . '.php';

        // Essaie chaque répertoire déclaré
        foreach ($dirs as $dir) {
            $fullPath = $root . '/' . $dir . '/' . $fileName;
            $fullPath = preg_replace('#[\\/]+#', '/', $fullPath); // Normalisation
            if (self::loadFile($fullPath)) {
                return true;
            }
        }

        // Fallback : racine du projet
        $fallback = $root . '/' . $fileName;
        $fallback = preg_replace('#[\\/]+#', '/', $fallback);
        if (self::loadFile($fallback)) {
            return true;
        }
        self::log("Classe non trouvée: {$className}", 'error');
        return false;
    }
}
