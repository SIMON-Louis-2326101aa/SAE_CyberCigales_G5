<?php // Balise d'ouverture PHP

namespace Tests\Integration; // Déclare le namespace Tests\Integration

use SAE_CyberCigales_G5\Modules\model\UserModel; // Importe UserModel du projet

/**
 * Tests d'intégration pour UserModel
 * 
 * @testdox Tests d'intégration - Gestion des utilisateurs
 * @group integration
 * @group database
 */
class UserModelIntegrationTest extends DatabaseTestCase // Hérite de DatabaseTestCase (fournit connexion DB et transactions)
{
    private UserModel $model; // Instance de UserModel utilisée dans les tests
    
    protected function setUp(): void // Méthode appelée AVANT chaque test
    {
        parent::setUp(); // Appelle setUp() de DatabaseTestCase (charge .env, crée connexion PDO, démarre transaction)
        
        $this->model = new UserModel(); // Crée une nouvelle instance de UserModel pour chaque test
    }
    
    /**
     * @testdox Retourne true lorsqu'un utilisateur existe avec l'email donné (insère un utilisateur en base avec INSERT, puis findByEmail() le retrouve et retourne true)
     */
    public function testFindByEmailReturnsTrueWhenExists(): void // Test : findByEmail() retourne true pour un utilisateur existant
    {
        $email = 'testuser@example.com'; // Email de test pour créer et chercher l'utilisateur
        
        $password = password_hash('Password123!', PASSWORD_DEFAULT); // Hash le mot de passe avec bcrypt, stocke dans $password
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare la requête SQL d'insertion avec placeholders
        
        $stmt->execute(['Test', 'User', $email, $password]); // Exécute la requête en remplaçant les placeholders (insère l'utilisateur en base)
        
        $result = $this->model->findByEmail($email); // Appelle findByEmail() pour chercher l'utilisateur, stocke le résultat (true/false) dans $result
        
        $this->assertTrue($result); // Vérifie que $result est true (l'utilisateur a été trouvé)
    }
    
    /**
     * @testdox Retourne false lorsqu'aucun utilisateur n'existe avec l'email donné (cherche un email inexistant en base, findByEmail() retourne false car aucune ligne trouvée)
     */
    public function testFindByEmailReturnsFalseWhenNotExists(): void // Test : findByEmail() retourne false pour un email inexistant
    {
        $user = $this->model->findByEmail('nonexistent@example.com'); // Cherche un utilisateur qui n'existe pas, stocke le résultat dans $user
        
        $this->assertFalse($user); // Vérifie que $user est false (l'utilisateur n'existe pas)
    }
    
    /**
     * @testdox Crée un utilisateur depuis pending_registrations après vérification (insère dans pending_registrations, puis createUserAfterVerification() copie les données vers users, vérifie avec SELECT)
     */
    public function testCreateUserAfterVerificationInsertsNewRecord(): void // Test : createUserAfterVerification() crée un utilisateur depuis pending_registrations
    {
        $nom = 'Nouveau'; // Nom de l'utilisateur
        $prenom = 'Utilisateur'; // Prénom de l'utilisateur
        $email = 'nouveau@example.com'; // Email de l'utilisateur
        $password = password_hash('SecurePass123!', PASSWORD_DEFAULT); // Hash le mot de passe
        
        $stmt = $this->pdo->prepare('INSERT INTO pending_registrations (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare l'insertion dans pending_registrations
        
        $stmt->execute([$nom, $prenom, $email, $password]); // Insère l'inscription en attente dans pending_registrations
        
        $result = $this->model->createUserAfterVerification($email); // Crée l'utilisateur depuis pending_registrations, stocke le résultat dans $result
        
        $this->assertTrue($result); // Vérifie que $result est true (l'utilisateur a été créé)
        
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?'); // Prépare la requête pour vérifier que l'utilisateur existe
        
        $stmt->execute([$email]); // Exécute la requête avec l'email
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC); // Récupère la ligne sous forme de tableau associatif, stocke dans $user
        
        $this->assertIsArray($user); // Vérifie que $user est un tableau (l'utilisateur existe)
        
        $this->assertEquals($email, $user['email']); // Vérifie que l'email correspond
    }
    
    /**
     * @testdox L'authentification réussit avec le bon mot de passe (hash le mot de passe avec password_hash(), stocke en base, puis authenticate() utilise password_verify() pour comparer, retourne l'utilisateur)
     */
    public function testAuthenticationSucceedsWithCorrectPassword(): void // Test : l'authentification réussit avec le bon mot de passe
    {
        $email = 'auth@example.com'; // Email de test
        
        $password = 'MyPassword123!'; // Mot de passe en clair (sera utilisé pour l'authentification)
        
        $hash = password_hash($password, PASSWORD_DEFAULT); // Hash le mot de passe pour le stocker en base
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare l'insertion de l'utilisateur
        
        $stmt->execute(['Auth', 'Test', $email, $hash]); // Insère l'utilisateur avec le mot de passe hashé
        
        $user = $this->model->authenticate($email, $password); // Authentifie avec email + mot de passe en clair (password_verify compare avec le hash), stocke le résultat dans $user
        
        $this->assertIsArray($user); // Vérifie que $user est un tableau (authentification réussie)
        
        $this->assertEquals($email, $user['email']); // Vérifie que l'email correspond (bon utilisateur retourné)
    }
    
    /**
     * @testdox L'authentification échoue avec un mauvais mot de passe (stocke un hash du bon mot de passe, puis authenticate() avec un mauvais mot de passe échoue car password_verify() retourne false)
     */
    public function testAuthenticationFailsWithWrongPassword(): void // Test : l'authentification échoue avec un mauvais mot de passe
    {
        $email = 'wrongauth@example.com'; // Email de test
        
        $correctPassword = 'CorrectPass123!'; // Bon mot de passe (sera hashé et stocké)
        
        $wrongPassword = 'WrongPass123!'; // Mauvais mot de passe (sera utilisé pour l'authentification, devrait échouer)
        
        $hash = password_hash($correctPassword, PASSWORD_DEFAULT); // Hash le BON mot de passe
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare l'insertion
        
        $stmt->execute(['Wrong', 'Auth', $email, $hash]); // Insère l'utilisateur avec le hash du BON mot de passe
        
        $user = $this->model->authenticate($email, $wrongPassword); // Essaie de s'authentifier avec le MAUVAIS mot de passe, stocke le résultat dans $user (devrait être false)
        
        $this->assertEmpty($user); // Vérifie que $user est vide (false/null) - l'authentification a bien échoué
    }
    
    /**
     * @testdox Retourne true pour un email existant et false pour un email inexistant (emailExists() fait un SELECT COUNT(*), retourne true si > 0, false sinon, teste les deux cas)
     */
    public function testEmailExistsReturnsTrueForExistingEmail(): void // Test : emailExists() retourne true pour un email existant, false sinon
    {
        $email = 'exists@example.com'; // Email de test
        
        $password = password_hash('Password123!', PASSWORD_DEFAULT); // Hash le mot de passe
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'); // Prépare l'insertion
        
        $stmt->execute(['Exists', 'Test', $email, $password]); // Insère l'utilisateur
        
        $exists = $this->model->emailExists($email); // Vérifie si l'email existe, stocke le résultat dans $exists
        
        $this->assertTrue($exists); // Vérifie que $exists est true (l'email existe)
        
        $notExists = $this->model->emailExists('notexists@example.com'); // Vérifie un email qui n'existe pas, stocke dans $notExists
        
        $this->assertFalse($notExists); // Vérifie que $notExists est false (l'email n'existe pas)
    }
}
