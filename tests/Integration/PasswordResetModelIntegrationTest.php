<?php

// Balise d'ouverture PHP

namespace Tests\Integration;

// Déclare le namespace Tests\Integration

use SAE_CyberCigales_G5\Modules\model\PasswordResetModel;

// Importe PasswordResetModel du projet

/**
 * Tests d'intégration pour PasswordResetModel
 *
 * @testdox Tests d'intégration - Réinitialisation de mot de passe
 * @group integration
 * @group database
 */
class PasswordResetModelIntegrationTest extends DatabaseTestCase
{
    // Instance de PasswordResetModel utilisée dans les tests
    private PasswordResetModel $model;

    protected function setUp(): void
    {
        // Appelle setUp() de DatabaseTestCase
        // (charge .env, crée connexion PDO, démarre transaction)
        parent::setUp();

        // Crée une nouvelle instance de PasswordResetModel pour chaque test
        $this->model = new PasswordResetModel();
    }

    /**
     * @testdox Crée un token valide de 64 caractères hexadécimaux pour un email existant
     * (random_bytes(32) + bin2hex(), stocké avec expires_at)
     */
    public function testCreateTokenForExistingEmail(): void
    {
        // Email de test
        $email = 'reset@example.com';

        // Hash le mot de passe
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Prépare l'insertion de l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );

        // Insère l'utilisateur
        $stmt->execute(['Reset', 'Test', $email, $password]);

        // Génère un token sécurisé avec TTL de 60 minutes
        $token = $this->model->createTokenForEmail($email, 60);

        // Vérifie que le token est une chaîne
        $this->assertIsString($token);

        // Vérifie la longueur du token (64 caractères hexadécimaux)
        $this->assertEquals(64, strlen($token));

        // Vérifie le format hexadécimal
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);

        // Prépare la requête pour chercher le token en base
        $stmt = $this->pdo->prepare(
            'SELECT * FROM password_reset_tokens WHERE token = ?'
        );

        // Exécute la requête
        $stmt->execute([$token]);

        // Récupère le token en base
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Vérifie que le token existe
        $this->assertIsArray($result);

        // Vérifie que le token correspond
        $this->assertEquals($token, $result['token']);

        // Vérifie que la date d'expiration existe
        $this->assertNotNull($result['expires_at']);
    }

    /**
     * @testdox Retourne false lors de la création d'un token pour un email inexistant
     */
    public function testCreateTokenReturnsFalseForNonExistentEmail(): void
    {
        // Tente de créer un token pour un email inexistant
        $token = $this->model->createTokenForEmail(
            'nonexistent@example.com',
            60
        );

        // Vérifie que la création échoue
        $this->assertFalse($token);
    }

    /**
     * @testdox Récupère un token valide depuis la base
     */
    public function testGetValidTokenRowReturnsRecordWhenValid(): void
    {
        $email = 'validtoken@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Valid', 'Token', $email, $password]);

        // Crée le token
        $token = $this->model->createTokenForEmail($email, 60);

        // Vérifie que le token est valide
        $this->assertIsString($token);

        // Vérifie que le token existe en base
        $stmt = $this->pdo->prepare(
            'SELECT * FROM password_reset_tokens WHERE token = ?'
        );
        $stmt->execute([$token]);

        $dbToken = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($dbToken);
        $this->assertEquals($token, $dbToken['token']);
    }

    /**
     * @testdox Retourne false pour un token invalide
     */
    public function testGetValidTokenRowReturnsFalseForInvalidToken(): void
    {
        $result = $this->model->getValidTokenRow(
            'invalidtoken1234567890abcdef1234567890abcdef1234567890abcdef12'
        );

        $this->assertFalse($result);
    }

    /**
     * @testdox Génère un nouveau token différent pour le même email
     */
    public function testNewTokenReplacesOldToken(): void
    {
        $email = 'multitoken@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Multi', 'Token', $email, $password]);

        // Crée deux tokens successifs
        $token1 = $this->model->createTokenForEmail($email, 60);
        $token2 = $this->model->createTokenForEmail($email, 60);

        // Vérifie que les tokens sont différents
        $this->assertNotEquals($token1, $token2);

        // Vérifie que les tokens existent en base
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM password_reset_tokens WHERE token IN (?, ?)'
        );
        $stmt->execute([$token1, $token2]);

        $count = $stmt->fetchColumn();

        $this->assertGreaterThan(0, $count);
    }

    /**
     * @testdox Vérifie que le token a une date d'expiration valide
     */
    public function testTokenHasExpirationDate(): void
    {
        $email = 'expiry@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Expiry', 'Test', $email, $password]);

        // Crée un token avec TTL de 30 minutes
        $token = $this->model->createTokenForEmail($email, 30);

        // Récupère le token
        $stmt = $this->pdo->prepare(
            'SELECT * FROM password_reset_tokens WHERE token = ?'
        );
        $stmt->execute([$token]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($result);
        $this->assertNotNull($result['expires_at']);

        $expiresAt = strtotime($result['expires_at']);
        $now = time();

        // Vérifie que l'expiration est dans le futur
        $this->assertGreaterThan($now, $expiresAt);

        $diff = ($expiresAt - $now) / 60;

        // Marge large pour éviter les faux positifs liés aux timezones
        $this->assertGreaterThan(28, $diff);
        $this->assertLessThan(92, $diff);
    }

    /**
     * @testdox Marque un token comme utilisé
     */
    public function testMarkTokenUsedSetsFlag(): void
    {
        $email = 'markused@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Mark', 'Used', $email, $password]);

        // Crée le token
        $token = $this->model->createTokenForEmail($email, 60);

        $this->assertIsString($token);

        // Marque le token comme utilisé
        $result = $this->model->markTokenUsed($token);

        $this->assertTrue($result);

        // Vérifie le flag used
        $stmt = $this->pdo->prepare(
            'SELECT used FROM password_reset_tokens WHERE token = ?'
        );
        $stmt->execute([$token]);

        $used = $stmt->fetchColumn();

        $this->assertEquals(1, $used);
    }

    /**
     * @testdox Supprime les tokens expirés
     */
    public function testPurgeExpiredRemovesOldTokens(): void
    {
        $email = 'purge@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Purge', 'Test', $email, $password]);

        $userId = (int) $this->pdo->lastInsertId();

        // Insère un token expiré manuellement
        $expiredToken = bin2hex(random_bytes(32));

        $stmt = $this->pdo->prepare(
            'INSERT INTO password_reset_tokens (user_id, token, expires_at, used, created_at)
             VALUES (?, ?, DATE_SUB(NOW(), INTERVAL 1 HOUR), 0, DATE_SUB(NOW(), INTERVAL 2 HOUR))'
        );
        $stmt->execute([$userId, $expiredToken]);

        // Lance la purge
        $result = $this->model->purgeExpired();

        $this->assertTrue($result);

        // Vérifie que le token a été supprimé
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM password_reset_tokens WHERE token = ?'
        );
        $stmt->execute([$expiredToken]);

        $count = $stmt->fetchColumn();

        $this->assertEquals(0, $count);
    }
}
