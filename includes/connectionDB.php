<?php
/**
 * Gestionnaire de connexion à la base de données (Singleton)
 * 
 * Cette classe gère la connexion PDO à la base de données MySQL.
 * Elle implémente le pattern Singleton pour garantir une seule connexion active.
 * 
 * Fonctionnalités :
 * - Connexion sécurisée via PDO
 * - Utilisation de variables d'environnement (.env) pour les identifiants
 * - Méthodes sécurisées pour insert, update, delete, select
 * - Protection contre les injections SQL (prepared statements)
 * - Validation stricte des identifiants de tables et colonnes
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

final class connectionDB
{
    /**
     * @var PDO Instance PDO de connexion à la base de données
     */
    private PDO $pdo;
    
    /**
     * @var connectionDB|null Instance unique de la classe (Singleton)
     */
    private static ?self $instance = null;

    /**
     * Constantes des clés d'environnement pour la connexion
     * Les valeurs réelles sont dans le fichier .env
     */
    private const DB_HOST = 'DB_HOST';
    private const DB_NAME = 'DB_NAME';
    private const DB_USER = 'DB_USER';
    private const DB_PASS = 'DB_PASS';

    /**
     * Constructeur privé (Singleton) - initialise la connexion PDO
     * 
     * Récupère les identifiants depuis les variables d'environnement (.env)
     * et crée la connexion PDO sécurisée.
     * 
     * @throws RuntimeException Si les variables d'environnement sont manquantes ou si la connexion échoue
     */
    private function __construct()
    {
        // Récupération des identifiants depuis les variables d'environnement
        $host = $_ENV[self::DB_HOST] ?? getenv(self::DB_HOST);
        $name = $_ENV[self::DB_NAME] ?? getenv(self::DB_NAME);
        $user = $_ENV[self::DB_USER] ?? getenv(self::DB_USER);
        $pass = $_ENV[self::DB_PASS] ?? getenv(self::DB_PASS);

        // Vérification que toutes les variables sont présentes
        if (!$host || !$name || !$user) {
            throw new RuntimeException('Les variables d\'environnement de connexion à la DB sont manquantes.');
        }

        // Construction du DSN (Data Source Name)
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);
        
        // Options PDO pour sécuriser la connexion
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Lance des exceptions en cas d'erreur
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Résultats en tableau associatif
            PDO::ATTR_EMULATE_PREPARES   => false,                     // Désactive l'émulation (vraies requêtes préparées)
        ];
        
        try {
            // Création de la connexion PDO
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // En production : log l'erreur + affiche un message générique
            throw new RuntimeException('Erreur de connexion à la base de données.');
        }
    }

    /**
     * Retourne l'instance unique de la classe (Singleton)
     * 
     * Crée l'instance si elle n'existe pas encore (lazy initialization).
     * 
     * @return self Instance unique de connectionDB
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Retourne l'instance PDO pour des requêtes personnalisées
     * 
     * @return PDO Instance PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Valide un identifiant SQL (nom de table ou de colonne)
     * 
     * Vérifie que le nom ne contient que des caractères alphanumériques et underscores.
     * Protection contre les injections SQL sur les noms de tables/colonnes.
     * 
     * @param string $name Nom à valider
     * @return string Nom validé
     * @throws InvalidArgumentException Si le nom contient des caractères invalides
     */
    private function assertIdentifier(string $name): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException("Identifiant SQL invalide: $name");
        }
        return $name;
    }

    /**
     * Construit une clause WHERE sécurisée à partir d'un tableau
     * 
     * Transforme un tableau associatif en clause WHERE avec paramètres liés.
     * Ex: ['email' => 'test@test.fr', 'id' => 5] devient " WHERE `email` = :w_email AND `id` = :w_id"
     * 
     * @param array $where Tableau associatif ['colonne' => valeur, ...]
     * @return array [0 => string SQL WHERE, 1 => array des paramètres liés]
     */
    private function buildWhere(array $where): array
    {
        $clauses = [];
        $params  = [];
        
        // Construction des clauses WHERE
        foreach ($where as $col => $val) {
            $col = $this->assertIdentifier($col);  // Validation du nom de colonne
            $param = ":w_$col";                    // Nom du paramètre lié
            $clauses[] = "`$col` = $param";        // Clause avec backticks pour sécurité
            $params[$param] = $val;                // Valeur du paramètre
        }
        
        // Construction de la chaîne SQL finale
        $sql = $clauses ? (' WHERE ' . implode(' AND ', $clauses)) : '';
        return [$sql, $params];
    }

    /**
     * Insère une ligne dans une table
     * 
     * Méthode sécurisée utilisant des requêtes préparées.
     * 
     * @param string $table Nom de la table
     * @param array $data Tableau associatif ['colonne' => valeur, ...]
     * @return int ID de la ligne insérée (lastInsertId)
     * @throws InvalidArgumentException Si aucune donnée n'est fournie
     */
    public function insert(string $table, array $data): int
    {
        $table = $this->assertIdentifier($table);
        
        // Validation : au moins une donnée doit être fournie
        if (!$data) {
            throw new InvalidArgumentException('Aucune donnée à insérer.');
        }

        $cols = [];          // Noms des colonnes
        $placeholders = [];  // Placeholders (:col)
        $params = [];        // Valeurs liées

        // Construction de la requête INSERT
        foreach ($data as $col => $val) {
            $col = $this->assertIdentifier($col);  // Validation du nom de colonne
            $cols[] = "`$col`";                    // Nom de colonne avec backticks
            $ph = ":$col";                         // Placeholder
            $placeholders[] = $ph;
            $params[$ph] = $val;                   // Valeur liée
        }

        // Requête SQL finale : INSERT INTO `table` (`col1`, `col2`) VALUES (:col1, :col2)
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', $cols),
            implode(', ', $placeholders)
        );

        // Exécution de la requête préparée
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        // Retour de l'ID de la ligne insérée
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Supprime des lignes d'une table
     * 
     * Méthode sécurisée qui REQUIERT une clause WHERE (pour éviter les suppressions accidentelles).
     * 
     * @param string $table Nom de la table
     * @param array $where Tableau associatif ['colonne' => valeur, ...]
     * @return int Nombre de lignes supprimées
     * @throws InvalidArgumentException Si aucune clause WHERE n'est fournie
     */
    public function delete(string $table, array $where): int
    {
        $table = $this->assertIdentifier($table);
        [$whereSql, $params] = $this->buildWhere($where);
        if ($whereSql === '') {
            throw new InvalidArgumentException('DELETE sans WHERE interdit.');
        }

        $sql = "DELETE FROM `$table`" . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function update(string $table, array $data, array $where): int
    {
        $table = $this->assertIdentifier($table);
        if (!$data) {
            throw new InvalidArgumentException('Aucune donnée à mettre à jour.');
        }

        $setParts = [];
        $params = [];
        foreach ($data as $col => $val) {
            $col = $this->assertIdentifier($col);
            $ph = ":u_$col";
            $setParts[] = "`$col` = $ph";
            $params[$ph] = $val;
        }

        [$whereSql, $whereParams] = $this->buildWhere($where);
        if ($whereSql === '') {
            throw new InvalidArgumentException('UPDATE sans WHERE interdit.');
        }
        $params += $whereParams;

        $sql = "UPDATE `$table` SET " . implode(', ', $setParts) . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function getAll(string $table, array $where = [], ?int $limit = null): array
    {
        $table = $this->assertIdentifier($table);
        [$whereSql, $params] = $this->buildWhere($where);
        $sql = "SELECT * FROM `$table`" . $whereSql;
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getElement(string $table, string $field, array $where): mixed
    {
        $table = $this->assertIdentifier($table);
        $field = $this->assertIdentifier($field);

        [$whereSql, $params] = $this->buildWhere($where);
        if ($whereSql === '') {
            throw new InvalidArgumentException('getElement requiert un WHERE.');
        }

        $sql = "SELECT `$field` FROM `$table`" . $whereSql . " LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return $row[$field] ?? null;
    }
}
