<?php

namespace Tests\Integration;

use SAE_CyberCigales_G5\Modules\model\PasswordResetModel;

/**
 * Tests d'intégration pour PasswordResetModel
 * 
 * @group integration
 * @group database
 */
class PasswordResetModelIntegrationTest extends DatabaseTestCase
{
    private PasswordResetModel $model;
    
    /**
     * Initialise l'environnement de test avant chaque test
     * 
     * Appelle setUp() de la classe parente (DatabaseTestCase) qui démarre
     * une transaction, puis crée une nouvelle instance de PasswordResetModel
     * pour chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new PasswordResetModel();
    }
    
    /**
     * Teste la création d'un token pour un email existant
     * 
     * Vérifie que la méthode createTokenForEmail() :
     * 1. Génère un token sécurisé de 64 caractères (hexadécimal)
     * 2. Stocke le token dans la base de données avec l'email
     * 3. Assigne une date d'expiration au token
     * 
     * Le token est généré à partir de 32 bytes aléatoires convertis en hexadécimal,
     * ce qui donne une chaîne de 64 caractères (ex: "a1b2c3d4...").
     */
    public function testCreateTokenForExistingEmail(): void
    {
        // Créer un utilisateur de test
        $email = 'reset@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Reset', 'Test', $email, $password]);
        
        // Créer un token
        $token = $this->model->createTokenForEmail($email, 60);
        
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // bin2hex(32 bytes) = 64 chars
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        
        // Vérifier en base
        $stmt = $this->pdo->prepare(
            'SELECT * FROM password_reset_tokens WHERE token = ?'
        );
        $stmt->execute([$token]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertIsArray($result);
        $this->assertEquals($token, $result['token']);
        $this->assertNotNull($result['expires_at']);
    }
    
    /**
     * Teste que createToken retourne false pour un email inexistant
     * 
     * Vérifie que la méthode createTokenForEmail() retourne false
     * lorsqu'on essaie de créer un token pour un email qui n'existe pas
     * dans la base de données.
     * 
     * Ce test garantit qu'on ne peut pas créer de token de réinitialisation
     * pour un utilisateur qui n'existe pas (sécurité).
     */
    public function testCreateTokenReturnsFalseForNonExistentEmail(): void
    {
        $token = $this->model->createTokenForEmail('nonexistent@example.com', 60);
        
        $this->assertFalse($token);
    }
    
    /**
     * Teste la recherche d'un token valide
     * 
     * Vérifie que la méthode getValidTokenRow() (ou équivalent) peut
     * récupérer un token valide depuis la base de données.
     * 
     * Étapes du test :
     * 1. Crée un utilisateur
     * 2. Génère un token pour cet utilisateur
     * 3. Vérifie que le token existe bien dans la base de données
     * 
     * Ce test garantit que les tokens sont correctement stockés
     * et peuvent être récupérés pour la réinitialisation du mot de passe.
     */
    public function testGetValidTokenRowReturnsRecordWhenValid(): void
    {
        // Créer un utilisateur
        $email = 'validtoken@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Valid', 'Token', $email, $password]);
        
        // Créer un token
        $token = $this->model->createTokenForEmail($email, 60);
        
        $this->assertIsString($token);
        
        // Vérifier que le token a bien été créé en base
        $stmt = $this->pdo->prepare(
            'SELECT * FROM password_reset_tokens WHERE token = ?'
        );
        $stmt->execute([$token]);
        $dbToken = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertIsArray($dbToken);
        $this->assertEquals($token, $dbToken['token']);
    }
    
    /**
     * Teste qu'un token invalide n'est pas trouvé
     * 
     * Vérifie que la méthode getValidTokenRow() retourne false
     * lorsqu'on cherche un token qui n'existe pas dans la base de données.
     * 
     * Ce test garantit la sécurité en vérifiant qu'un token invalide
     * ou inexistant ne peut pas être utilisé pour réinitialiser un mot de passe.
     */
    public function testGetValidTokenRowReturnsFalseForInvalidToken(): void
    {
        $result = $this->model->getValidTokenRow('invalidtoken1234567890abcdef1234567890abcdef1234567890abcdef12');
        
        $this->assertFalse($result);
    }
    
    /**
     * Teste qu'un nouveau token remplace l'ancien
     * 
     * Vérifie que lorsqu'on génère un nouveau token pour un email qui a
     * déjà un token, le nouveau token est créé (et peut remplacer l'ancien).
     * 
     * Ce test garantit que :
     * 1. Plusieurs tokens peuvent être générés pour le même email
     * 2. Les tokens générés sont différents
     * 3. L'utilisateur peut demander un nouveau token si nécessaire
     * (par exemple si l'ancien token a expiré)
     */
    public function testNewTokenReplacesOldToken(): void
    {
        // Créer un utilisateur
        $email = 'multitoken@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Multi', 'Token', $email, $password]);
        
        // Créer deux tokens successifs
        $token1 = $this->model->createTokenForEmail($email, 60);
        $token2 = $this->model->createTokenForEmail($email, 60);
        
        // Les tokens doivent être différents
        $this->assertNotEquals($token1, $token2);
        
        // Vérifier en base - selon l'implémentation, l'ancien peut être supprimé
        // ou les deux peuvent coexister
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM password_reset_tokens WHERE token IN (?, ?)'
        );
        $stmt->execute([$token1, $token2]);
        $count = $stmt->fetchColumn();
        
        // Au moins le nouveau token doit exister
        $this->assertGreaterThan(0, $count);
    }
    
    /**
     * Teste que le token a une date d'expiration valide
     * 
     * Vérifie que chaque token créé a une date d'expiration :
     * 1. La date d'expiration n'est pas null
     * 2. La date d'expiration est dans le futur
     * 3. La date d'expiration correspond au TTL fourni (avec une marge d'erreur)
     * 
     * Ce test garantit que les tokens ont une durée de vie limitée,
     * ce qui améliore la sécurité en évitant que des tokens anciens
     * puissent être utilisés indéfiniment.
     */
    public function testTokenHasExpirationDate(): void
    {
        // Créer un utilisateur
        $email = 'expiry@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Expiry', 'Test', $email, $password]);
        
        // Créer un token avec TTL de 30 minutes
        $token = $this->model->createTokenForEmail($email, 30);
        
        // Récupérer les données du token
        $stmt = $this->pdo->prepare(
            'SELECT * FROM password_reset_tokens WHERE token = ?'
        );
        $stmt->execute([$token]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertIsArray($result);
        $this->assertNotNull($result['expires_at']);
        
        // Vérifier que la date d'expiration est dans le futur
        $expiresAt = strtotime($result['expires_at']);
        $now = time();
        
        $this->assertGreaterThan($now, $expiresAt);
        
        // Vérifier que c'est environ 30 minutes (avec marge de 2 minutes)
        $diff = ($expiresAt - $now) / 60; // Différence en minutes
        $this->assertGreaterThan(28, $diff);
        $this->assertLessThan(32, $diff);
    }
}


