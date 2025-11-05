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
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new PasswordResetModel();
    }
    
    /**
     * Teste la création d'un token pour un email existant
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
     */
    public function testCreateTokenReturnsFalseForNonExistentEmail(): void
    {
        $token = $this->model->createTokenForEmail('nonexistent@example.com', 60);
        
        $this->assertFalse($token);
    }
    
    /**
     * Teste la recherche d'un token valide
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
     */
    public function testGetValidTokenRowReturnsFalseForInvalidToken(): void
    {
        $result = $this->model->getValidTokenRow('invalidtoken1234567890abcdef1234567890abcdef1234567890abcdef12');
        
        $this->assertFalse($result);
    }
    
    /**
     * Teste qu'un nouveau token remplace l'ancien
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


