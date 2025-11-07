<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use SAE_CyberCigales_G5\includes\ConnectionDB;
use PDO;

/**
 * Classe de base pour les tests d'intégration avec base de données
 * 
 * Utilise des transactions pour isoler les tests :
 * - BEGIN TRANSACTION avant chaque test
 * - ROLLBACK après chaque test
 * 
 * Ainsi les tests n'affectent jamais réellement la base de données.
 * 
 * @group integration
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * Connexion PDO pour les tests
     * 
     * @var PDO
     */
    protected PDO $pdo;
    
    /**
     * Instance de ConnectionDB
     * 
     * @var ConnectionDB
     */
    protected ConnectionDB $db;
    
    /**
     * Initialise la connexion et démarre une transaction avant chaque test
     * 
     * Cette méthode s'exécute automatiquement avant chaque test d'intégration.
     * Elle :
     * 1. Charge les variables d'environnement depuis le fichier .env
     * 2. Récupère une connexion à la base de données
     * 3. Démarre une transaction (BEGIN TRANSACTION)
     * 
     * Grâce aux transactions, toutes les modifications faites pendant le test
     * seront annulées automatiquement à la fin (ROLLBACK), ce qui garantit
     * que les tests n'affectent jamais la vraie base de données.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Charger les variables d'environnement si nécessaire
        if (!isset($_ENV['DB_HOST'])) {
            $rootDir = dirname(__DIR__, 2);
            if (file_exists($rootDir . '/config/.env')) {
                $dotenv = \Dotenv\Dotenv::createImmutable($rootDir . '/config', '.env');
                $dotenv->load();
            }
        }
        
        // Récupérer la connexion
        $this->db = ConnectionDB::getInstance();
        $this->pdo = $this->db->getPdo();
        
        // Démarrer une transaction pour isoler le test
        $this->pdo->beginTransaction();
    }
    
    /**
     * Annule la transaction après chaque test (ROLLBACK)
     * 
     * Cette méthode s'exécute automatiquement après chaque test d'intégration.
     * Elle annule toutes les modifications faites pendant le test en appelant
     * rollBack() sur la transaction.
     * 
     * Ainsi, même si un test insère, modifie ou supprime des données,
     * la base de données revient à son état initial après le test.
     * Cela permet de :
     * - Tester avec des données réelles sans risque
     * - Exécuter les tests plusieurs fois sans pollution
     * - Garantir que chaque test commence avec une base propre
     */
    protected function tearDown(): void
    {
        // Annuler toutes les modifications du test
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        
        parent::tearDown();
    }
    
    /**
     * Helper : Nettoie une table spécifique (pour setup de test)
     * 
     * Supprime toutes les lignes d'une table. Utile pour préparer
     * un état de base de données connu avant un test.
     * 
     * Note : Comme cette méthode est appelée dans une transaction,
     * les suppressions seront annulées après le test (ROLLBACK).
     * 
     * @param string $tableName Nom de la table à vider
     * @return void
     */
    protected function truncateTable(string $tableName): void
    {
        $this->pdo->exec("DELETE FROM {$tableName}");
    }
    
    /**
     * Helper : Compte le nombre de lignes dans une table
     * 
     * Utile pour vérifier qu'un test a bien inséré ou supprimé
     * le nombre attendu de lignes.
     * 
     * @param string $tableName Nom de la table à compter
     * @return int Nombre de lignes dans la table
     */
    protected function countRowsInTable(string $tableName): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$tableName}");
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Helper : Récupère la dernière ligne insérée d'une table
     * 
     * Récupère la ligne avec l'ID le plus élevé (supposant que la table
     * a une colonne 'id' auto-incrémentée).
     * 
     * Utile pour vérifier les données d'un enregistrement qui vient
     * d'être créé pendant un test.
     * 
     * @param string $tableName Nom de la table
     * @return array|false Tableau associatif avec les données de la ligne, ou false si aucune ligne
     */
    protected function getLastInsertedRow(string $tableName): array|false
    {
        $stmt = $this->pdo->query("SELECT * FROM {$tableName} ORDER BY id DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

