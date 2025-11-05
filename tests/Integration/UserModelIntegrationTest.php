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
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new UserModel();
    }
    
    /**
     * Teste la recherche d'un utilisateur par email
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
     */
    public function testFindByEmailReturnsFalseWhenNotExists(): void
    {
        $user = $this->model->findByEmail('nonexistent@example.com');
        
        $this->assertFalse($user);
    }
    
    /**
     * Teste la création d'un utilisateur après vérification d'email
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

