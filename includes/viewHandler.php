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

        $A_params = $parametres;
        if (!is_readable($S_file)) {
            throw new Exception("Fichier de vue non trouvé : " . $S_file);
        }
        extract($parametres);

        ob_start();
        include $S_file;
        ob_end_flush();
    }
}