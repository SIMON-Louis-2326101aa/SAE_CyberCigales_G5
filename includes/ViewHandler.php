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
    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    /**
     * Démarre le buffer global si aucun buffer n'est actif.
     */
    public static function bufferStart(): void
    {
        if (ob_get_level() === 0) {
            ob_start();
            self::log('Buffer global démarré', 'ok', [
                'ob_level' => ob_get_level(),
            ]);
        } else {
            self::log('Buffer déjà actif (aucune action)', 'file', [
                'ob_level' => ob_get_level(),
            ]);
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
            self::log('Buffer collecté', 'ok', [
                'content_length' => strlen((string)$content),
            ]);
            return (string)$content;
        }

        self::log('Aucun buffer à collecter', 'file');
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
            self::log('Nom de vue invalide détecté', 'error', [
                'view' => $loc,
            ]);
            throw new InvalidArgumentException('Nom de vue invalide.');
        }

        // Construction des chemins absolus
        $viewFile   = rtrim(Constant::viewDir(), '/\\') . '/' . ltrim($loc, '/\\') . '.php';
        $layoutDir  = rtrim(Constant::includesDir(), '/\\') . '/layout';
        $headerFile = $layoutDir . '/header.php';
        $footerFile = $layoutDir . '/footer.php';

        // Vérifications de lisibilité
        if (!is_readable($viewFile)) {
            self::log('Fichier de vue non trouvé', 'error', [
                'view' => $loc,
                'view_file' => $viewFile,
            ]);
            throw new RuntimeException("Fichier de vue non trouvé : {$viewFile}");
        }

        self::log('Préparation rendu vue', 'file', [
            'view' => $loc,
            'view_file' => $viewFile,
            'has_header' => is_readable($headerFile),
            'has_footer' => is_readable($footerFile),
            'params_count' => count($parametres),
        ]);

        if (is_readable($headerFile)) {
            self::log('Header prêt à être inclus', 'file', [
                'header_file' => $headerFile,
            ]);
        } else {
            self::log('Header absent ou illisible', 'warn', [
                'header_file' => $headerFile,
            ]);
        }

        if (is_readable($footerFile)) {
            self::log('Footer prêt à être inclus', 'file', [
                'footer_file' => $footerFile,
            ]);
        } else {
            self::log('Footer absent ou illisible', 'warn', [
                'footer_file' => $footerFile,
            ]);
        }

        // Mise à disposition des paramètres dans la portée de la vue
        if (!empty($parametres)) {
            extract($parametres, EXTR_SKIP);
            self::log('Paramètres extraits pour la vue', 'file', [
                'view' => $loc,
                'params_count' => count($parametres),
            ]);
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
        self::log('Vue affichée', 'ok', [
            'view' => $loc,
        ]);
    }
}
