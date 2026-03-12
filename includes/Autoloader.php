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

    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
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
            self::log('Fichier autoload chargé', 'ok', [
                'path' => $path,
            ]);
            return true;
        }

        self::log('Fichier autoload non lisible', 'file', [
            'path' => $path,
        ]);

        return false;
    }

    /**
     * Autoload principal.
     * Charge automatiquement une classe en cherchant dans les différents répertoires.
     * Exemple: "ControllerHandler" -> <dir>/ControllerHandler.php
     */
    public static function classLoad(string $className): bool
    {
        $originalClassName = $className;

        if (false !== $lastNsPos = strrpos($className, '\\')) {
            $className = substr($className, $lastNsPos + 1);
        }

        // Vérifie la classe Constant (chemins)
        if (!class_exists(Constant::class)) {
            self::log('Classe Constant introuvable pendant autoload', 'error', [
                'requested_class' => $originalClassName,
            ]);
            return false;
        }

        // Racine projet + dossiers à parcourir
        $root = rtrim(Constant::indexDir(), '/\\');
        $dirs = [
            rtrim(Constant::INCLUDES_DIR, '/\\'),
            rtrim(Constant::MODEL_DIR, '/\\'),
            rtrim(Constant::VIEW_DIR, '/\\'),
            rtrim(Constant::CONTROLLER_DIR, '/\\'),
        ];

        $fileName = $className . '.php';

        self::log('Début autoload classe', 'file', [
            'requested_class' => $originalClassName,
            'resolved_class' => $className,
            'file_name' => $fileName,
            'directories_count' => count($dirs),
        ]);

        // Essaie chaque répertoire déclaré
        foreach ($dirs as $dir) {
            $fullPath = $root . '/' . $dir . '/' . $fileName;
            $fullPath = preg_replace('#[\\/]+#', '/', $fullPath);

            self::log('Tentative autoload dans répertoire déclaré', 'file', [
                'requested_class' => $originalClassName,
                'path' => $fullPath,
            ]);

            if (self::loadFile($fullPath)) {
                self::log('Classe chargée via autoload', 'ok', [
                    'requested_class' => $originalClassName,
                    'path' => $fullPath,
                ]);
                return true;
            }
        }

        // Fallback : racine du projet
        $fallback = $root . '/' . $fileName;
        $fallback = preg_replace('#[\\/]+#', '/', $fallback);

        self::log('Tentative autoload fallback racine', 'file', [
            'requested_class' => $originalClassName,
            'path' => $fallback,
        ]);

        if (self::loadFile($fallback)) {
            self::log('Classe chargée via fallback racine', 'ok', [
                'requested_class' => $originalClassName,
                'path' => $fallback,
            ]);
            return true;
        }

        self::log('Classe non trouvée après autoload complet', 'error', [
            'requested_class' => $originalClassName,
            'resolved_class' => $className,
            'file_name' => $fileName,
        ]);

        return false;
    }
}
