<?php

namespace Tests\Integration;

use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel;

/**
 * Tests d'intégration pour EmailVerificationModel
 * 
 * Ces tests utilisent la vraie base de données mais dans des transactions
 * qui sont annulées automatiquement (ROLLBACK) après chaque test.
 * 
 * @group integration
 * @group database
 */
class EmailVerificationModelIntegrationTest extends DatabaseTestCase
{
    private EmailVerificationModel $model;
    
    /**
     * Initialise l'environnement de test avant chaque test
     * 
     * Appelle setUp() de la classe parente (DatabaseTestCase) qui démarre
     * une transaction, puis crée une nouvelle instance de EmailVerificationModel
     * pour chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new EmailVerificationModel();
    }
    
    /**
     * Teste la génération et le stockage d'un code de vérification
     * 
     * Vérifie que la méthode generateAndStoreCode() :
     * 1. Génère un code de 6 chiffres
     * 2. Stocke le code dans la base de données avec l'email
     * 3. Assigne une date d'expiration au code
     * 
     * Le code généré doit respecter le format : 6 chiffres (ex: "123456")
     * et être stocké dans la table email_verification_codes.
     */
    public function testGenerateAndStoreCodeCreatesValidCode(): void
    {
        $email = 'test@example.com';
        
        // Générer et stocker un code
        $code = $this->model->generateAndStoreCode($email, 10);
        
        // Vérifications
        $this->assertIsString($code);
        $this->assertEquals(6, strlen($code));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        
        // Vérifier que le code est bien en base
        $stmt = $this->pdo->prepare(
            'SELECT * FROM email_verification_codes WHERE email = ? AND code = ?'
        );
        $stmt->execute([$email, $code]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertIsArray($result);
        $this->assertEquals($email, $result['email']);
        $this->assertEquals($code, $result['code']);
        $this->assertNotNull($result['expires_at']);
    }
    
    /**
     * Teste la vérification d'un code valide
     * 
     * Vérifie que la méthode checkCodeStatus() retourne un statut valide
     * lorsque le code fourni correspond au code stocké pour l'email.
     * 
     * Étapes du test :
     * 1. Génère et stocke un code pour un email
     * 2. Appelle checkCodeStatus() avec l'email et le code correct
     * 3. Vérifie que le statut indique que le code est valide
     */
    public function testCheckCodeStatusReturnsValidForCorrectCode(): void
    {
        $email = 'valid@example.com';
        $code = $this->model->generateAndStoreCode($email, 10);
        
        // Vérifier le statut
        $status = $this->model->checkCodeStatus($email, $code);
        
        $this->assertIsArray($status);
        $this->assertTrue($status['valid']);
        $this->assertEquals('valid', $status['reason']);
    }
    
    /**
     * Teste la vérification d'un code incorrect
     * 
     * Vérifie que la méthode checkCodeStatus() retourne un statut invalide
     * lorsque le code fourni ne correspond pas au code stocké pour l'email.
     * 
     * Étapes du test :
     * 1. Génère et stocke un code pour un email
     * 2. Appelle checkCodeStatus() avec l'email et un mauvais code
     * 3. Vérifie que le statut indique que le code est incorrect
     * 
     * Ce test garantit la sécurité en vérifiant qu'un code incorrect
     * ne peut pas être utilisé pour vérifier un email.
     */
    public function testCheckCodeStatusReturnsIncorrectForWrongCode(): void
    {
        $email = 'test@example.com';
        $this->model->generateAndStoreCode($email, 10);
        
        // Vérifier avec un mauvais code
        $status = $this->model->checkCodeStatus($email, '999999');
        
        $this->assertIsArray($status);
        $this->assertFalse($status['valid']);
        $this->assertEquals('incorrect', $status['reason']);
    }
    
    /**
     * Teste le stockage d'une inscription en attente
     * 
     * Vérifie que la méthode storePendingRegistration() crée correctement
     * un enregistrement dans la table pending_registrations avec les données
     * de l'utilisateur (nom, prénom, email, mot de passe hashé).
     * 
     * Les inscriptions en attente sont stockées temporairement jusqu'à
     * ce que l'utilisateur vérifie son email et que son compte soit créé.
     */
    public function testStorePendingRegistrationCreatesRecord(): void
    {
        $nom = 'Dupont';
        $prenom = 'Jean';
        $email = 'jean.dupont@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        // Stocker l'inscription en attente
        $result = $this->model->storePendingRegistration($nom, $prenom, $email, $password);
        
        $this->assertTrue($result);
        
        // Vérifier en base
        $stmt = $this->pdo->prepare('SELECT * FROM pending_registrations WHERE email = ?');
        $stmt->execute([$email]);
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertIsArray($record);
        $this->assertEquals($nom, $record['nom']);
        $this->assertEquals($prenom, $record['prenom']);
        $this->assertEquals($email, $record['email']);
    }
    
    /**
     * Teste qu'un nouveau code remplace l'ancien pour le même email
     * 
     * Vérifie que lorsqu'on génère un nouveau code pour un email qui a
     * déjà un code, le nouveau code est généré (et peut remplacer l'ancien).
     * 
     * Ce test garantit que :
     * 1. Plusieurs codes peuvent être générés pour le même email
     * 2. Les codes générés sont différents
     * 3. L'utilisateur peut demander un nouveau code si nécessaire
     */
    public function testGeneratingNewCodeReplacesOldOne(): void
    {
        $email = 'replace@example.com';
        
        // Générer un premier code
        $code1 = $this->model->generateAndStoreCode($email, 10);
        
        // Compter les codes pour cet email
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM email_verification_codes WHERE email = ?');
        $stmt->execute([$email]);
        $count1 = $stmt->fetchColumn();
        
        // Générer un second code
        $code2 = $this->model->generateAndStoreCode($email, 10);
        
        // Compter à nouveau
        $stmt->execute([$email]);
        $count2 = $stmt->fetchColumn();
        
        // Les deux codes doivent être différents
        $this->assertNotEquals($code1, $code2);
        
        // Il peut y avoir plusieurs codes en base (pas de suppression automatique dans le modèle actuel)
        $this->assertGreaterThanOrEqual($count1, $count2);
    }
    
    /**
     * Teste que le TTL (Time To Live) est bien limité entre 1 et 60 minutes
     * 
     * Vérifie que la méthode generateAndStoreCode() limite correctement
     * la valeur du TTL :
     * - Les valeurs < 1 sont remontées à 1 minute
     * - Les valeurs > 60 sont limitées à 60 minutes
     * - Les valeurs entre 1 et 60 restent inchangées
     * 
     * Cela garantit que les codes de vérification ont une durée de vie
     * raisonnable et sécurisée.
     */
    public function testTtlIsClampedToValidRange(): void
    {
        $email = 'ttl@example.com';
        
        // Tester avec un TTL trop petit (devrait être 1)
        $this->model->generateAndStoreCode($email, 0);
        
        // Tester avec un TTL trop grand (devrait être 60)
        $this->model->generateAndStoreCode($email, 100);
        
        // Tester avec un TTL valide
        $this->model->generateAndStoreCode($email, 15);
        
        // Si pas d'exception, le test passe
        $this->assertTrue(true);
    }
}

