<?php
/**
 * Autoloader - Chargement automatique des classes
 * 
 * Ce fichier implémente l'autoloading de classes pour le projet.
 * Il cherche automatiquement les fichiers de classes dans les différents répertoires.
 * 
 * Fonctionnement :
 * - Quand une classe est instanciée (ex: new userModel()), PHP appelle automatiquement ClassLoad()
 * - La méthode cherche le fichier correspondant dans includes/, Modules/model/, Modules/view/, Modules/controller/
 * - Si trouvé, le fichier est chargé (require) et la classe devient disponible
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

require 'constant.php';

final class autoloader
{
    /**
     * Charge un fichier PHP s'il est lisible
     * 
     * @param string $S_toLoad Chemin complet vers le fichier à charger
     * @return bool True si le fichier a été chargé, False sinon
     */
    private static function _load(string $S_toLoad): bool
    {
        if (is_readable($S_toLoad)) {
            require $S_toLoad;
            return true;
        }
        return false;
    }
    
    /**
     * Charge automatiquement une classe en cherchant dans les différents répertoires
     * 
     * Cette méthode est appelée automatiquement par PHP quand une classe est utilisée
     * mais pas encore chargée.
     * 
     * @param string $S_className Nom de la classe à charger
     * @return bool True si la classe a été trouvée et chargée, False sinon
     */
    public static function ClassLoad(string $S_className): bool
    {
        // Liste des répertoires où chercher les classes
        $directories = [
            Constant::INCLUDES_DIR,
            Constant::MODEL_DIR,
            Constant::VIEW_DIR,
            Constant::CONTROLLER_DIR
        ];

        // Recherche dans chaque répertoire
        foreach ($directories as $directory) {
            $S_file = Constant::indexDir() . $directory . "$S_className.php";
            if (self::_load($S_file)) {
                return true;  // Classe trouvée et chargée
            }
        }

        // Message de débogage si la classe n'est pas trouvée
        echo "Classe non trouvée : " . $S_className . "<br>";
        return false;
    }
}

// Enregistrement de l'autoloader dans PHP
// À partir de maintenant, PHP appellera automatiquement autoloader::ClassLoad() pour toute classe non trouvée
spl_autoload_register('autoloader::classLoad');