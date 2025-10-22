<?php
/**
 * Classe abstraite de base pour l'accès à la base de données
 * 
 * Cette classe fournit une connexion PDO partagée à tous les modèles qui en héritent.
 * Elle implémente le pattern Singleton pour garantir une seule connexion active.
 * 
 * Tous les modèles du projet doivent hériter de cette classe pour accéder à la BDD.
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

require_once __DIR__ . '/../../includes/connectionDB.php';

abstract class database
{
    /**
     * @var PDO Instance partagée de la connexion à la base de données
     */
    private static $_bdd;

    /**
     * Initialise la connexion à la base de données (méthode privée)
     * 
     * Utilise la classe connectionDB (Singleton) pour obtenir l'instance PDO.
     * Cette méthode n'est appelée qu'une seule fois.
     * 
     * @return void
     */
    private static function setBdd()
    {
        // Récupération de l'instance unique de connexion
        $connexion = connectionDB::getInstance();
        self::$_bdd = $connexion->getPdo();
    }

    /**
     * Retourne l'instance PDO de connexion à la base de données
     * 
     * Initialise la connexion si elle n'existe pas encore (lazy loading).
     * Toutes les classes qui héritent de database peuvent utiliser cette méthode.
     * 
     * @return PDO Instance de connexion à la base de données
     */
    public function getBdd()
    {
        // Initialisation paresseuse : crée la connexion seulement si nécessaire
        if (self::$_bdd == null) {
            self::setBdd();
        }
        return self::$_bdd;
    }
}