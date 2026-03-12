<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

use InvalidArgumentException;
use PDO;
use PDOException;
use RuntimeException;

class ConnectionDB
{
    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    private PDO $pdo;
    private static ?self $instance = null;

    public function __construct()
    {
        // Lecture via .env ou variables système
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
        $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER');
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS');

        if (!$host || !$name || !$user) {
            self::log('Variables DB manquantes', 'error', [
                'has_host' => (bool)$host,
                'has_name' => (bool)$name,
                'has_user' => (bool)$user,
            ]);
            throw new RuntimeException("Les variables d'environnement DB sont manquantes.");
        }

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
            self::log('Connexion DB réussie', 'ok', [
                'db_host' => $host,
                'db_name' => $name,
            ]);
        } catch (PDOException $e) {
            self::log('Erreur PDO lors de la connexion', 'error', [
                'db_host' => $host,
                'db_name' => $name,
                'pdo_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            throw new RuntimeException('Erreur de connexion à la base de données.');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::log('Création de l’instance singleton ConnectionDB', 'info');
            self::$instance = new self();
        } else {
            self::log('Réutilisation de l’instance singleton ConnectionDB', 'file');
        }

        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /** Validation stricte des identifiants SQL (table/colonnes) */
    private function assertIdentifier(string $name): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            self::log('Identifiant SQL invalide détecté', 'error', [
                'identifier' => $name,
            ]);
            throw new InvalidArgumentException("Identifiant SQL invalide: $name");
        }

        return $name;
    }

    /** Construit un WHERE préparé à partir d'un tableau */
    private function buildWhere(array $where): array
    {
        $clauses = [];
        $params  = [];

        foreach ($where as $col => $val) {
            $col = $this->assertIdentifier($col);
            $param = ":w_$col";
            $clauses[] = "`$col` = $param";
            $params[$param] = $val;
        }

        $sql = $clauses ? (' WHERE ' . implode(' AND ', $clauses)) : '';

        return [$sql, $params];
    }

    public function insert(string $table, array $data): int
    {
        $table = $this->assertIdentifier($table);

        if (!$data) {
            self::log('Tentative INSERT sans données', 'warn', [
                'table' => $table,
            ]);
            throw new InvalidArgumentException('Aucune donnée à insérer.');
        }

        $cols = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $col => $val) {
            $col = $this->assertIdentifier($col);
            $cols[] = "`$col`";
            $ph = ":$col";
            $placeholders[] = $ph;
            $params[$ph] = $val;
        }

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', $cols),
            implode(', ', $placeholders)
        );

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $lastId = (int)$this->pdo->lastInsertId();

            self::log('INSERT réussi', 'ok', [
                'table' => $table,
                'columns_count' => count($data),
                'last_insert_id' => $lastId,
            ]);

            return $lastId;
        } catch (PDOException $e) {
            self::log('Erreur PDO pendant INSERT', 'error', [
                'table' => $table,
                'columns_count' => count($data),
                'pdo_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function delete(string $table, array $where): int
    {
        $table = $this->assertIdentifier($table);
        [$whereSql, $params] = $this->buildWhere($where);

        if ($whereSql === '') {
            self::log('Tentative DELETE sans WHERE bloquée', 'error', [
                'table' => $table,
            ]);
            throw new InvalidArgumentException('DELETE sans WHERE interdit.');
        }

        $sql = "DELETE FROM `$table`" . $whereSql;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rowCount = $stmt->rowCount();

            self::log('DELETE exécuté', 'file', [
                'table' => $table,
                'where_count' => count($where),
                'affected_rows' => $rowCount,
            ]);

            return $rowCount;
        } catch (PDOException $e) {
            self::log('Erreur PDO pendant DELETE', 'error', [
                'table' => $table,
                'where_count' => count($where),
                'pdo_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function update(string $table, array $data, array $where): int
    {
        $table = $this->assertIdentifier($table);

        if (!$data) {
            self::log('Tentative UPDATE sans données', 'warn', [
                'table' => $table,
            ]);
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
            self::log('Tentative UPDATE sans WHERE bloquée', 'error', [
                'table' => $table,
                'data_count' => count($data),
            ]);
            throw new InvalidArgumentException('UPDATE sans WHERE interdit.');
        }

        $params += $whereParams;

        $sql = "UPDATE `$table` SET " . implode(', ', $setParts) . $whereSql;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rowCount = $stmt->rowCount();

            self::log('UPDATE exécuté', 'file', [
                'table' => $table,
                'data_count' => count($data),
                'where_count' => count($where),
                'affected_rows' => $rowCount,
            ]);

            return $rowCount;
        } catch (PDOException $e) {
            self::log('Erreur PDO pendant UPDATE', 'error', [
                'table' => $table,
                'data_count' => count($data),
                'where_count' => count($where),
                'pdo_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getAll(string $table, array $where = [], ?int $limit = null): array
    {
        $table = $this->assertIdentifier($table);
        [$whereSql, $params] = $this->buildWhere($where);

        $sql = "SELECT * FROM `$table`" . $whereSql;
        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$limit;
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            self::log('SELECT * exécuté', 'file', [
                'table' => $table,
                'where_count' => count($where),
                'limit' => $limit,
                'rows_count' => count($rows),
            ]);

            return $rows;
        } catch (PDOException $e) {
            self::log('Erreur PDO pendant SELECT *', 'error', [
                'table' => $table,
                'where_count' => count($where),
                'limit' => $limit,
                'pdo_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getElement(string $table, string $field, array $where): mixed
    {
        $table = $this->assertIdentifier($table);
        $field = $this->assertIdentifier($field);

        [$whereSql, $params] = $this->buildWhere($where);

        if ($whereSql === '') {
            self::log('Tentative getElement sans WHERE bloquée', 'error', [
                'table' => $table,
                'field' => $field,
            ]);
            throw new InvalidArgumentException('getElement requiert un WHERE.');
        }

        $sql = "SELECT `$field` FROM `$table`" . $whereSql . ' LIMIT 1';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch();
            $value = $row[$field] ?? null;

            self::log('SELECT élément exécuté', 'ok', [
                'table' => $table,
                'field' => $field,
                'where_count' => count($where),
                'found' => $value !== null,
            ]);

            return $value;
        } catch (PDOException $e) {
            self::log('Erreur PDO pendant SELECT élément', 'error', [
                'table' => $table,
                'field' => $field,
                'where_count' => count($where),
                'pdo_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}