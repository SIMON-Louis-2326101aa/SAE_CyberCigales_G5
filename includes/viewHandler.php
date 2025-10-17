<?php

final class viewHandler
{
    public static function bufferStart(): void
    {
        ob_start();
    }

    public static function bufferCollect(): string
    {
        return ob_get_clean();
    }

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