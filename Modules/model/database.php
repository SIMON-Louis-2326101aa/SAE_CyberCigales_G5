<?php
/**
 * Classe abstraite de base pour les modèles de données
 * 
 * Cette classe fournit l'accès à la base de données via PDO pour tous les modèles.
 * Elle utilise le pattern Singleton de connectionDB pour garantir une seule connexion.
 * 
 * Fonctionnalités :
 * - Accès centralisé à la base de données
 * - Gestion automatique de la connexion
 * - Pattern Singleton pour les performances
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

require_once __DIR__ . '/../../includes/connectionDB.php';

abstract class database
{
    /**
     * @var PDO|null Instance PDO de connexion à la base de données
     */
    private static $_bdd;

    /**
     * Initialise la connexion à la base de données
     * 
     * Cette méthode utilise le singleton connectionDB pour obtenir
     * une instance PDO sécurisée et configurée.
     * 
     * @return void
     */
    private static function setBdd()
    {
        // Utiliser connectionDB pour obtenir la connexion
        $connexion = connectionDB::getInstance();
        self::$_bdd = $connexion->getPdo();
    }

    /**
     * Retourne l'instance PDO de la base de données
     * 
     * Cette méthode implémente le pattern Singleton pour garantir
     * qu'une seule connexion à la base de données est utilisée.
     * 
     * @return PDO Instance PDO de connexion à la base de données
     */
    public function getBdd()
    {
        if (self::$_bdd == null) {
            self::setBdd();
        }
        return self::$_bdd;
    }
}
?>