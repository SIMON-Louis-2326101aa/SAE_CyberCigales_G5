<?php

require 'constant.php';

final class autoloader
{

    private static function _load(string $S_toLoad): bool
    {
        if (is_readable($S_toLoad)) {
            require $S_toLoad;
            return true;
        }
        return false;
    }
    public static function ClassLoad(string $S_className): bool
    {
        $directories = [
            Constant::INCLUDES_DIR,
            Constant::MODEL_DIR,
            Constant::VIEW_DIR,
            Constant::CONTROLLER_DIR
        ];

        foreach ($directories as $directory) {
            $S_file = Constant::indexDir() . $directory . "$S_className.php";
            if (self::_load($S_file)) {
                return true;
            }
        }

        echo "Classe non trouvée : " . $S_className . "<br>"; // Ajout pour débogage
        return false;
    }
}

// Enregistrement de l'autoloader
spl_autoload_register('autoloader::classLoad');