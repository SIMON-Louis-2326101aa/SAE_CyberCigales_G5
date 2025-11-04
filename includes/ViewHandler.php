<?php

/**
 * ViewHandler
 * - Gère le buffering global (démarrage/collecte).
 * - Rend une vue avec header et footer communs.
 * - Sécurise le nom de la vue
 *
 * Important :
 * - On n'utilise PAS ob_start() ici pendant show() pour éviter de casser
 *   le pipeline index.php -> bufferStart() -> bufferCollect().
 * - Les fichiers header/footer sont inclus si lisibles.
 */

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

use InvalidArgumentException;
use RuntimeException;

final class ViewHandler
{
    /**
     * Démarre le buffer global si aucun buffer n'est actif.
     */
    public static function bufferStart(): void
    {
        if (ob_get_level() === 0) {
            ob_start();
            if (function_exists('log_console')) {
                log_console('Buffer global démarré', 'ok'); // ✅
            }
        } else {
            if (function_exists('log_console')) {
                log_console('Buffer déjà actif (aucune action)', 'info'); // ℹ️
            }
        }
    }

    /**
     * Récupère et vide le buffer global.
     * @return string Contenu bufferisé (ou chaîne vide si aucun buffer).
     */
    public static function bufferCollect(): string
    {
        if (ob_get_level() > 0) {
            $content = ob_get_clean();
            if (function_exists('log_console')) {
                log_console('Buffer collecté', 'ok'); // ✅
            }
            return (string)$content;
        }
        if (function_exists('log_console')) {
            log_console('Aucun buffer à collecter', 'info'); // ℹ️
        }
        return '';
    }

    /**
     * Affiche une vue (avec header et footer communs).
     * @param string $loc        Chemin de la vue relatif à Modules/view (ex: 'auth/login')
     * @param array  $parametres Données à extraire dans la vue (EXTR_SKIP)
     */
    public static function show(string $loc, array $parametres = []): void
    {
        // Sécurisation du nom de vue (autorise lettres/chiffres/_/- et sous-dossiers avec '/')
        if (!preg_match('/^[A-Za-z0-9_\/-]+$/', $loc)) {
            if (function_exists('log_console')) {
                log_console("Nom de vue invalide : {$loc}", 'error'); // ❌
            }
            throw new InvalidArgumentException("Nom de vue invalide.");
        }

        // Construction des chemins absolus
        $viewFile   = rtrim(Constant::viewDir(), '/\\') . '/' . ltrim($loc, '/\\') . '.php';
        $layoutDir  = rtrim(Constant::includesDir(), '/\\') . '/layout';
        $headerFile = $layoutDir . '/header.php';
        $footerFile = $layoutDir . '/footer.php';

        // Vérifications de lisibilité
        if (!is_readable($viewFile)) {
            if (function_exists('log_console')) {
                log_console("Fichier de vue non trouvé : {$viewFile}", 'error'); // ❌
            }
            throw new RuntimeException("Fichier de vue non trouvé : {$viewFile}");
        }

        // Log des fichiers ciblés
        if (function_exists('log_console')) {
            log_console("Rendu vue: {$viewFile}", 'file'); // 📄
            //if (is_readable($headerFile)) log_console("Header inclus: {$headerFile}", 'file'); // 📄
            //if (is_readable($footerFile)) log_console("Footer inclus: {$footerFile}", 'file'); // 📄
        }

        // Mise à disposition des paramètres dans la portée de la vue
        // EXTR_SKIP pour ne pas écraser d'éventuelles variables existantes
        if (!empty($parametres)) {
            extract($parametres, EXTR_SKIP);
        }

        // Inclusion du header si disponible
        if (is_readable($headerFile)) {
            include $headerFile;
        }

        // Inclusion de la vue principale
        include $viewFile;

        // Inclusion du footer si disponible
        if (is_readable($footerFile)) {
            include $footerFile;
        }

        // Pas d'ob_end_flush() ici : on laisse index.php récupérer via bufferCollect()
        if (function_exists('log_console')) {
            log_console("Vue affichée: {$loc}", 'ok'); // ✅
        }
    }
}
