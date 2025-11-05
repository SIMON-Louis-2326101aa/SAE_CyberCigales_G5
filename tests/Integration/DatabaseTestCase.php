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
     * Toutes les modifications sont annulées automatiquement
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
     * @param string $tableName Nom de la table à vider
     */
    protected function truncateTable(string $tableName): void
    {
        $this->pdo->exec("DELETE FROM {$tableName}");
    }
    
    /**
     * Helper : Compte le nombre de lignes dans une table
     * 
     * @param string $tableName Nom de la table
     * @return int Nombre de lignes
     */
    protected function countRowsInTable(string $tableName): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$tableName}");
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Helper : Récupère la dernière ligne insérée d'une table
     * 
     * @param string $tableName Nom de la table
     * @return array|false Tableau associatif ou false
     */
    protected function getLastInsertedRow(string $tableName): array|false
    {
        $stmt = $this->pdo->query("SELECT * FROM {$tableName} ORDER BY id DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

