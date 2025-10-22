<?php
/**
 * Gestionnaire de vues (View Handler)
 * 
 * Cette classe gère l'affichage des vues avec header et footer automatiques.
 * Elle utilise le système de buffer de sortie PHP pour construire les pages.
 * 
 * Fonctionnalités :
 * - Affichage automatique du header et footer
 * - Gestion des paramètres passés aux vues
 * - Système de buffer pour éviter l'affichage prématuré
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

final class viewHandler
{
    /**
     * Démarre le buffer de sortie
     * 
     * Cette méthode initialise le système de buffer PHP pour capturer
     * tout le contenu qui sera affiché avant de l'envoyer au navigateur.
     * 
     * @return void
     */
    public static function bufferStart(): void
    {
        ob_start();
    }

    /**
     * Récupère le contenu du buffer et le vide
     * 
     * Cette méthode retourne tout le contenu qui a été capturé dans le buffer
     * depuis le dernier appel à bufferStart() et vide le buffer.
     * 
     * @return string Le contenu capturé dans le buffer
     */
    public static function bufferCollect(): string
    {
        return ob_get_clean();
    }

    /**
     * Affiche une vue complète avec header et footer
     * 
     * Cette méthode affiche une page complète en incluant automatiquement
     * le header, la vue demandée et le footer. Elle gère aussi les paramètres
     * passés à la vue.
     * 
     * @param string $loc Chemin vers la vue (sans extension .php)
     * @param array $parametres Paramètres à passer à la vue
     * @return void
     * @throws Exception Si le fichier de vue n'existe pas
     */
    public static function show(string $loc, $parametres = array()): void
    {
        $S_file = Constant::viewDir() . $loc . '.php';
        $headerFile = __DIR__ . '/layout/header.php';
        $footerFile = __DIR__ . '/layout/footer.php';

        $A_params = $parametres;
        if (!is_readable($S_file)) {
            throw new Exception("Fichier de vue non trouvé : " . $S_file);
        }

        ob_start();

        // Pour avoir le header sur tous les pages
        if (is_readable($headerFile)) {
            include $headerFile;
        }

        // Prendre les paramètres et Mettre le contenu de la vue
        extract($parametres);
        include $S_file;

        // Pour avoir le footer sur tous les pages
        if (is_readable($footerFile)) {
            include $footerFile;
        }

        ob_end_flush();
    }
}