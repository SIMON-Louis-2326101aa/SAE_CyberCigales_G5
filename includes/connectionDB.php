<?php
final class connectionDB
{
    private PDO $pdo;
    private static ?self $instance = null;

    // Idéalement: lire depuis l'environnement
    private const DB_HOST = 'mysql-escapethecode.alwaysdata.net';
    private const DB_NAME = 'escapethecode_bd';
    private const DB_USER = '433487';
    private const DB_PASS = 'SAECyberCigales';

    private function __construct()
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', self::DB_HOST, self::DB_NAME);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
        } catch (PDOException $e) {
            // En prod: log + message générique
            throw new RuntimeException('Erreur de connexion à la base de données.');
        }
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /** Validation stricte des identifiants SQL (table/colonnes) */
    private function assertIdentifier(string $name): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException("Identifiant SQL invalide: $name");
        }
        return $name;
    }

    /** Construit un WHERE préparé à partir d'un tableau ['col' => valeur, ...] */
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$this->pdo->lastInsertId();
    }

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
