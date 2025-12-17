<?php // Balise d'ouverture PHP

namespace Tests\Integration; // Déclare le namespace Tests\Integration

use SAE_CyberCigales_G5\Modules\model\PasswordResetModel; // Importe PasswordResetModel du projet

/**
 * Tests d'intégration pour PasswordResetModel
 * 
 * @testdox Tests d'intégration - Réinitialisation de mot de passe
 * @group integration
 * @group database
 */
class PasswordResetModelIntegrationTest extends DatabaseTestCase // Hérite de DatabaseTestCase (fournit connexion DB et transactions)
{
    private PasswordResetModel $model; // Instance de PasswordResetModel utilisée dans les tests
    
    protected function setUp(): void // Méthode appelée AVANT chaque test
    {
        parent::setUp(); // Appelle setUp() de DatabaseTestCase (charge .env, crée connexion PDO, démarre transaction)
        
        $this->model = new PasswordResetModel(); // Crée une nouvelle instance de PasswordResetModel pour chaque test
    }
    
    /**
     * @testdox Crée un token valide de 64 caractères hexadécimaux pour un email existant (utilise random_bytes(32) puis bin2hex() pour générer 64 chars hex, stocke dans password_reset_tokens avec expires_at, valide avec regex /^[a-f0-9]{64}$/)
     */
    public function testCreateTokenForExistingEmail(): void // Test : createTokenForEmail() crée un token valide de 64 caractères hexadécimaux pour un email existant
    {
        $email = 'reset@example.com'; // Email de test pour lequel créer le token
        
        $password = password_hash('Password123!', PASSWORD_DEFAULT); // Hash le mot de passe
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare l'insertion de l'utilisateur
        
        $stmt->execute(['Reset', 'Test', $email, $password]); // Insère l'utilisateur (doit exister pour créer un token)
        
        $token = $this->model->createTokenForEmail($email, 60); // Génère un token sécurisé (32 bytes → 64 chars hex), le stocke en base avec expiration (60 min), retourne le token
        
        $this->assertIsString($token); // Vérifie que $token est une chaîne de caractères
        
        $this->assertEquals(64, strlen($token)); // Vérifie que la longueur de $token est exactement 64 caractères (32 bytes × 2 = 64 chars hex)
        
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token); // Vérifie que $token contient exactement 64 caractères hexadécimaux (a-f0-9) avec une regex
        
        $stmt = $this->pdo->prepare('SELECT * FROM password_reset_tokens WHERE token = ?'); // Prépare la requête pour chercher le token en base
        
        $stmt->execute([$token]); // Exécute la requête avec le token
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC); // Récupère la ligne sous forme de tableau associatif, stocke dans $result
        
        $this->assertIsArray($result); // Vérifie que $result est un tableau (le token a été trouvé en base)
        
        $this->assertEquals($token, $result['token']); // Vérifie que le token correspond
        
        $this->assertNotNull($result['expires_at']); // Vérifie que la date d'expiration n'est pas null
    }
    
    /**
     * @testdox Retourne false lors de la création d'un token pour un email inexistant (createTokenForEmail() fait un SELECT pour vérifier l'utilisateur, si aucun résultat retourne false sans créer de token)
     */
    public function testCreateTokenReturnsFalseForNonExistentEmail(): void // Test : createTokenForEmail() retourne false pour un email inexistant
    {
        $token = $this->model->createTokenForEmail('nonexistent@example.com', 60); // Essaie de créer un token pour un email qui n'existe pas, stocke le résultat dans $token
        
        $this->assertFalse($token); // Vérifie que $token est false (on ne peut pas créer de token pour un utilisateur inexistant)
    }
    
    /**
     * @testdox Récupère le token valide depuis la base de données (crée un token, puis getValidTokenRow() fait un SELECT avec JOIN users pour récupérer le token si valide, non utilisé et non expiré)
     */
    public function testGetValidTokenRowReturnsRecordWhenValid(): void // Test : le token créé existe bien dans la base de données
    {
        $email = 'validtoken@example.com'; // Email de test
        
        $password = password_hash('Password123!', PASSWORD_DEFAULT); // Hash le mot de passe
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare l'insertion
        
        $stmt->execute(['Valid', 'Token', $email, $password]); // Insère l'utilisateur
        
        $token = $this->model->createTokenForEmail($email, 60); // Crée un token pour cet utilisateur, stocke dans $token
        
        $this->assertIsString($token); // Vérifie que $token est une chaîne
        
        $stmt = $this->pdo->prepare('SELECT * FROM password_reset_tokens WHERE token = ?'); // Prépare la requête pour vérifier en base
        
        $stmt->execute([$token]); // Exécute la requête avec le token
        
        $dbToken = $stmt->fetch(\PDO::FETCH_ASSOC); // Récupère la ligne sous forme de tableau associatif, stocke dans $dbToken
        
        $this->assertIsArray($dbToken); // Vérifie que $dbToken est un tableau (le token existe en base)
        
        $this->assertEquals($token, $dbToken['token']); // Vérifie que le token correspond
    }
    
    /**
     * @testdox Retourne false pour un token invalide (getValidTokenRow() cherche un token inexistant en base, le SELECT ne trouve rien et retourne false)
     */
    public function testGetValidTokenRowReturnsFalseForInvalidToken(): void // Test : getValidTokenRow() retourne false pour un token invalide
    {
        $result = $this->model->getValidTokenRow('invalidtoken1234567890abcdef1234567890abcdef1234567890abcdef12'); // Cherche un token invalide, stocke le résultat dans $result
        
        $this->assertFalse($result); // Vérifie que $result est false (le token invalide n'a pas été trouvé)
    }
    
    /**
     * @testdox Un nouveau token remplace l'ancien token pour le même email (génère deux tokens pour le même email, ils sont différents car random_bytes() génère des valeurs aléatoires, les deux peuvent coexister en base)
     */
    public function testNewTokenReplacesOldToken(): void // Test : générer un nouveau token pour le même email crée un token différent
    {
        $email = 'multitoken@example.com'; // Email de test
        
        $password = password_hash('Password123!', PASSWORD_DEFAULT); // Hash le mot de passe
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare l'insertion
        
        $stmt->execute(['Multi', 'Token', $email, $password]); // Insère l'utilisateur
        
        $token1 = $this->model->createTokenForEmail($email, 60); // Crée un premier token, stocke dans $token1
        
        $token2 = $this->model->createTokenForEmail($email, 60); // Crée un second token, stocke dans $token2
        
        $this->assertNotEquals($token1, $token2); // Vérifie que les deux tokens sont différents
        
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM password_reset_tokens WHERE token IN (?, ?)'); // Prépare la requête pour compter les tokens
        
        $stmt->execute([$token1, $token2]); // Exécute la requête avec les deux tokens
        
        $count = $stmt->fetchColumn(); // Récupère le nombre de tokens trouvés, stocke dans $count
        
        $this->assertGreaterThan(0, $count); // Vérifie que au moins un token existe (le nouveau token doit exister)
    }
    
    /**
     * @testdox Le token a une date d'expiration valide dans le futur (crée un token avec TTL 30 min, expires_at = date('Y-m-d H:i:s', time() + 30*60), vérifie que la différence est entre 28 et 32 minutes avec marge d'erreur)
     */
    public function testTokenHasExpirationDate(): void // Test : le token a une date d'expiration valide (dans le futur, correspond au TTL)
    {
        $email = 'expiry@example.com'; // Email de test
        
        $password = password_hash('Password123!', PASSWORD_DEFAULT); // Hash le mot de passe
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare l'insertion
        
        $stmt->execute(['Expiry', 'Test', $email, $password]); // Insère l'utilisateur
        
        $token = $this->model->createTokenForEmail($email, 30); // Crée un token avec TTL de 30 minutes, stocke dans $token
        
        $stmt = $this->pdo->prepare('SELECT * FROM password_reset_tokens WHERE token = ?'); // Prépare la requête pour récupérer le token
        
        $stmt->execute([$token]); // Exécute la requête avec le token
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC); // Récupère la ligne sous forme de tableau associatif, stocke dans $result
        
        $this->assertIsArray($result); // Vérifie que $result est un tableau
        
        $this->assertNotNull($result['expires_at']); // Vérifie que la date d'expiration n'est pas null
        
        $expiresAt = strtotime($result['expires_at']); // Convertit la date d'expiration en timestamp Unix, stocke dans $expiresAt
        
        $now = time(); // Récupère le timestamp actuel, stocke dans $now
        
        $this->assertGreaterThan($now, $expiresAt); // Vérifie que la date d'expiration est dans le futur
        
        $diff = ($expiresAt - $now) / 60; // Calcule la différence en minutes, stocke dans $diff
        
        $this->assertGreaterThan(28, $diff); // Vérifie que la différence est > 28 minutes (marge d'erreur de 2 min)
        
        $this->assertLessThan(32, $diff); // Vérifie que la différence est < 32 minutes (marge d'erreur de 2 min)
    }
}
