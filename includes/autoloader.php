<?php
declare(strict_types=1);

/**
 * Autoloader.php
 * - Parcourt les répertoires déclarés dans Constant::*_DIR.
 * - Construit des chemins absolus à partir de Constant::indexDir().
 */

require __DIR__ . '/constant.php';

final class autoloader
{
    /** Empêche l'instanciation. */
    private function __construct() {}

    /**
     * Charge un fichier PHP s'il est lisible
     */
    private static function log(string $message, string $type = 'info'): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type); // ℹ️ ✅ ❌ 📄 🔊
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
            self::log("Chargé: {$path}", 'ok');     // ✅
            return true;
        }
        self::log("Non lisible: {$path}", 'file');   // 📄
        return false;
    }

    /**
     * Autoload principal.
     *  Charge automatiquement une classe en cherchant dans les différents répertoire
     * Exemple: "controllerHandler" -> <dir>/controllerHandler.php
     */
    public static function classLoad(string $className): bool
    {
        // Vérifie la classe Constant (chemins)
        if (!class_exists('Constant')) {
            self::log('Classe Constant introuvable (includes/constant.php non chargé ?)', 'error'); // ❌
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

        self::log("Classe non trouvée: {$className}", 'error'); // ❌
        return false;
    }
}

// Enregistrement de l’autoload
spl_autoload_register([autoloader::class, 'classLoad']);

// Log global (on n'appelle pas la méthode privée)
if (function_exists('log_console')) {
    log_console('Autoloader enregistré', 'info'); // ℹ️
}