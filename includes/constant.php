<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cybercigales');
define('DB_USER', 'root');
define('DB_PASS', '');

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
