<?php

namespace Tests\Integration;

use SAE_CyberCigales_G5\Modules\model\UserModel;

/**
 * Tests d'intégration pour UserModel
 * 
 * @group integration
 * @group database
 */
class UserModelIntegrationTest extends DatabaseTestCase
{
    private UserModel $model;
    
    /**
     * Initialise l'environnement de test avant chaque test
     * 
     * Appelle setUp() de la classe parente (DatabaseTestCase) qui démarre
     * une transaction, puis crée une nouvelle instance de UserModel
     * pour chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new UserModel();
    }
    
    /**
     * Teste la recherche d'un utilisateur par email (utilisateur existant)
     * 
     * Vérifie que la méthode findByEmail() retourne true lorsqu'un utilisateur
     * avec l'email fourni existe dans la base de données.
     * 
     * Étapes du test :
     * 1. Crée un utilisateur de test directement en base de données
     * 2. Appelle findByEmail() avec l'email de l'utilisateur créé
     * 3. Vérifie que le résultat est true
     */
    public function testFindByEmailReturnsTrueWhenExists(): void
    {
        // Créer un utilisateur de test directement en base
        $email = 'testuser@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Test', 'User', $email, $password]);
        
        // Chercher l'utilisateur (findByEmail retourne bool)
        $result = $this->model->findByEmail($email);
        
        $this->assertTrue($result);
    }
    
    /**
     * Teste que findByEmail retourne false pour un email inexistant
     * 
     * Vérifie que la méthode findByEmail() retourne false lorsqu'aucun
     * utilisateur avec l'email fourni n'existe dans la base de données.
     * 
     * Ce test garantit que la méthode gère correctement le cas où
     * l'utilisateur n'existe pas (pas d'erreur, retourne simplement false).
     */
    public function testFindByEmailReturnsFalseWhenNotExists(): void
    {
        $user = $this->model->findByEmail('nonexistent@example.com');
        
        $this->assertFalse($user);
    }
    
    /**
     * Teste la création d'un utilisateur après vérification d'email
     * 
     * Vérifie que la méthode createUserAfterVerification() crée correctement
     * un utilisateur dans la table users à partir d'une inscription en attente
     * (pending_registrations).
     * 
     * Étapes du test :
     * 1. Crée une inscription en attente dans pending_registrations
     * 2. Appelle createUserAfterVerification() avec l'email
     * 3. Vérifie que l'utilisateur a bien été créé dans la table users
     * 4. Vérifie que les données de l'utilisateur sont correctes
     */
    public function testCreateUserAfterVerificationInsertsNewRecord(): void
    {
        $nom = 'Nouveau';
        $prenom = 'Utilisateur';
        $email = 'nouveau@example.com';
        $password = password_hash('SecurePass123!', PASSWORD_DEFAULT);
        
        // D'abord créer une inscription en attente
        $stmt = $this->pdo->prepare(
            'INSERT INTO pending_registrations (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$nom, $prenom, $email, $password]);
        
        // Créer l'utilisateur après vérification
        $result = $this->model->createUserAfterVerification($email);
        
        $this->assertTrue($result);
        
        // Vérifier que l'utilisateur est bien en base
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
    }
    
    /**
     * Teste l'authentification avec un mot de passe correct
     * 
     * Vérifie que la méthode authenticate() retourne les données de l'utilisateur
     * lorsque l'email et le mot de passe sont corrects.
     * 
     * Étapes du test :
     * 1. Crée un utilisateur avec un mot de passe hashé
     * 2. Appelle authenticate() avec l'email et le mot de passe en clair
     * 3. Vérifie que la méthode retourne un tableau avec les données de l'utilisateur
     * 4. Vérifie que l'email correspond
     * 
     * Note : Le mot de passe est hashé avec password_hash() et vérifié
     * avec password_verify() dans la méthode authenticate().
     */
    public function testAuthenticationSucceedsWithCorrectPassword(): void
    {
        $email = 'auth@example.com';
        $password = 'MyPassword123!';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Créer un utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Auth', 'Test', $email, $hash]);
        
        // Tester l'authentification
        $user = $this->model->authenticate($email, $password);
        
        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
    }
    
    /**
     * Teste que l'authentification échoue avec un mauvais mot de passe
     * 
     * Vérifie que la méthode authenticate() ne retourne pas les données
     * de l'utilisateur lorsque le mot de passe est incorrect.
     * 
     * Étapes du test :
     * 1. Crée un utilisateur avec un mot de passe hashé
     * 2. Appelle authenticate() avec l'email et un mauvais mot de passe
     * 3. Vérifie que la méthode retourne false ou un tableau vide
     * 
     * Ce test garantit la sécurité en vérifiant qu'un utilisateur ne peut
     * pas s'authentifier avec un mot de passe incorrect.
     */
    public function testAuthenticationFailsWithWrongPassword(): void
    {
        $email = 'wrongauth@example.com';
        $correctPassword = 'CorrectPass123!';
        $wrongPassword = 'WrongPass123!';
        $hash = password_hash($correctPassword, PASSWORD_DEFAULT);
        
        // Créer un utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Wrong', 'Auth', $email, $hash]);
        
        // Tester l'authentification avec le mauvais mot de passe
        $user = $this->model->authenticate($email, $wrongPassword);
        
        // L'authentification échoue (retourne false ou null selon l'implémentation)
        $this->assertEmpty($user);
    }
    
    /**
     * Teste la méthode emailExists
     * 
     * Vérifie que la méthode emailExists() retourne :
     * - true si l'email existe dans la base de données
     * - false si l'email n'existe pas
     * 
     * Ce test couvre deux cas :
     * 1. Un email existant doit retourner true
     * 2. Un email inexistant doit retourner false
     * 
     * Utile pour vérifier la disponibilité d'un email lors de l'inscription.
     */
    public function testEmailExistsReturnsTrueForExistingEmail(): void
    {
        $email = 'exists@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        // Créer un utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Exists', 'Test', $email, $password]);
        
        // Vérifier que l'email existe
        $exists = $this->model->emailExists($email);
        $this->assertTrue($exists);
        
        // Vérifier qu'un email inexistant retourne false
        $notExists = $this->model->emailExists('notexists@example.com');
        $this->assertFalse($notExists);
    }
}

