<?php
require_once __DIR__ . '/../../includes/connectionDB.php';

abstract class database
{
    private static $_bdd;

    private static function setBdd()
    {
        // Utiliser connectionDB pour obtenir la connexion
        $connexion = connectionDB::getInstance();
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