<?php
/**
 * Script de configuration de la base de données
 * Ce script vous aide à créer la table login_attempts
 */

require_once __DIR__ . '/vendor/autoload.php';

// Chargement des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/config', 'database.env');
$dotenv->load();

// Récupération des paramètres de connexion
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'cybercigales_db';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';

echo "=== Configuration de la Base de Données ===\n";
echo "Host: $host\n";
echo "Base de données: $dbname\n";
echo "Utilisateur: $username\n";
echo "Mot de passe: " . (empty($password) ? '(vide)' : '***') . "\n\n";

try {
    // Connexion à la base de données
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ Connexion à la base de données réussie !\n\n";
    
    // Lecture du fichier SQL
    $sqlFile = __DIR__ . '/database_setup.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Fichier SQL non trouvé : $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Exécution du script SQL
    echo "🔧 Création de la table login_attempts...\n";
    $pdo->exec($sql);
    
    echo "✅ Table login_attempts créée avec succès !\n";
    echo "🎉 Configuration de la base de données terminée !\n";
    
    // Vérification que la table a été créée
    $stmt = $pdo->query("SHOW TABLES LIKE 'login_attempts'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Vérification : La table login_attempts existe bien.\n";
    } else {
        echo "❌ Erreur : La table login_attempts n'a pas été créée.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion à la base de données :\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "🔧 Solutions possibles :\n";
    echo "   1. Vérifiez que MySQL/MariaDB est démarré\n";
    echo "   2. Vérifiez vos paramètres dans config/database.env\n";
    echo "   3. Créez la base de données '$dbname' si elle n'existe pas\n";
    echo "   4. Vérifiez que l'utilisateur '$username' a les droits nécessaires\n";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== Fin du script ===\n";
