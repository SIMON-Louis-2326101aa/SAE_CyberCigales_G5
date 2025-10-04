<?php
define('DB_HOST', 'mysql-escapethecode.alwaysdata.net');
define('DB_NAME', 'escapethecode_bd');
define('DB_USER', '433487');
define('DB_PASS', 'SAECyberCigales');

// Connexion PDO globale
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur connexion BD : " . $e->getMessage());
}
