<?php
require_once __DIR__ . '/../../includes/connexionDB.php';

abstract class database
{
    private static $_bdd;

    private static function setBdd()
    {
        // Utiliser connexionDB pour obtenir la connexion
        $connexion = connexionDB::getInstance();
        self::$_bdd = $connexion->getPdo();
    }

    public function getBdd()
    {
        if (self::$_bdd == null) {
            self::setBdd();
        }
        return self::$_bdd;
    }
}
?>