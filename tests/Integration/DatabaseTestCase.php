<?php

// Balise d'ouverture PHP

namespace Tests\Integration;

// Déclare le namespace Tests\Integration

use PHPUnit\Framework\TestCase; // Importe TestCase de PHPUnit

use SAE_CyberCigales_G5\includes\ConnectionDB; // Importe ConnectionDB (gère la connexion à la base de données)

use PDO;

// Importe PDO (interface pour accéder à la base de données)

/**
 * Classe de base pour les tests d'intégration avec base de données
 *
 * @group integration
 */
abstract class DatabaseTestCase extends TestCase // Classe abstraite : ne peut pas être instanciée directement,
                                                // sert de base pour d'autres tests
{
    protected PDO $pdo; // Connexion PDO à la base de données (accessible dans cette classe et ses classes filles)

    protected ConnectionDB $db; // Instance de ConnectionDB (accessible dans cette classe et ses classes filles)

    /**
     * Initialise la connexion et démarre une transaction avant chaque test
     */
    protected function setUp(): void // Méthode appelée automatiquement AVANT chaque test
    {
        parent::setUp(); // Appelle setUp() de la classe parente (TestCase) pour initialiser PHPUnit

        if (!isset($_ENV['DB_HOST'])) { // Si la variable d'environnement DB_HOST n'existe pas
            $rootDir = dirname(__DIR__, 2); // Remonte de 2 niveaux dans l'arborescence pour trouver la racine du projet

            if (file_exists($rootDir . '/config/.env')) { // Si le fichier .env existe
                $dotenv = \Dotenv\Dotenv::createImmutable($rootDir . '/config', '.env');
                // Crée une instance Dotenv pour charger le fichier .env

                $dotenv->load(); // Charge les variables du fichier .env dans $_ENV (DB_HOST, DB_NAME, etc.)
            }
        }

        $this->db = ConnectionDB::getInstance(); // Récupère l'instance singleton de ConnectionDB

        $this->pdo = $this->db->getPdo(); // Récupère l'objet PDO depuis ConnectionDB (connexion à la base de données)

        $this->pdo->beginTransaction(); // Démarre une transaction SQL
        // (toutes les modifications seront annulées à la fin du test avec rollBack)
    }

    /**
     * Annule la transaction après chaque test (ROLLBACK)
     */
    protected function tearDown(): void // Méthode appelée automatiquement APRÈS chaque test
    {
        if ($this->pdo->inTransaction()) { // Si une transaction est en cours
            $this->pdo->rollBack(); // Annule toutes les modifications faites dans la transaction
            // (INSERT, UPDATE, DELETE sont annulés)
        }

        parent::tearDown(); // Appelle tearDown() de la classe parente (TestCase) pour nettoyer PHPUnit
    }

    /**
     * Helper : Nettoie une table spécifique (pour setup de test)
     */
    protected function truncateTable(string $tableName): void // Méthode helper pour vider une table
    {
        $this->pdo->exec("DELETE FROM {$tableName}"); // Exécute une requête SQL DELETE(sera annulé par rollBack)
    }

    /**
     * Helper : Compte le nombre de lignes dans une table
     */
    protected function countRowsInTable(string $tableName): int // Méthode helper compte nombre lignes dans une table
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$tableName}"); // Exécute une requête SQL COUNT pour
        // compter les lignes, stocke le résultat dans $stmt

        return (int)$stmt->fetchColumn(); // Récupère la première colonne (le nombre de lignes) et
        // la convertit en entier, puis retourne cette valeur
    }

    /**
     * Helper : Récupère la dernière ligne insérée d'une table
     */
    protected function getLastInsertedRow(string $tableName): array|false
    {
        $stmt = $this->pdo->query("SELECT * FROM {$tableName} ORDER BY id DESC LIMIT 1"); // Exécute une requête
        // SQL pour récupérer la dernière ligne (triée par id décroissant, limitée à 1)

        return $stmt->fetch(PDO::FETCH_ASSOC); // Récupère une ligne et la retourne sous forme de tableau
        // associatif (clés = noms de colonnes), ou false si aucune ligne
    }
}
