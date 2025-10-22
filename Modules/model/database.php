<?php
/**
 * Classe abstraite de base pour la gestion de la base de données
 * 
 * Cette classe abstraite fournit les fonctionnalités de base pour la connexion
 * à la base de données. Elle utilise le pattern Singleton via connectionDB
 * pour s'assurer qu'une seule connexion est établie.
 * 
 * Fonctionnalités :
 * - Gestion centralisée de la connexion PDO
 * - Pattern Singleton pour éviter les connexions multiples
 * - Méthode getBdd() accessible à tous les modèles
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

require_once __DIR__ . '/../../includes/connectionDB.php';

abstract class database
{
    /**
     * @var PDO|null Instance de connexion à la base de données
     */
    private static $_bdd;

    /**
     * Initialise la connexion à la base de données
     * 
     * Cette méthode privée utilise le pattern Singleton de connectionDB
     * pour obtenir une instance unique de connexion PDO.
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
     * Retourne l'instance de connexion à la base de données
     * 
     * Cette méthode publique permet aux classes héritières d'accéder
     * à la connexion PDO. Si la connexion n'existe pas encore,
     * elle l'initialise automatiquement.
     * 
     * @return PDO Instance de connexion PDO
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