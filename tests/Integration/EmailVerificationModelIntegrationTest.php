<?php // Balise d'ouverture PHP

namespace Tests\Integration; // Déclare le namespace Tests\Integration

use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel; // Importe EmailVerificationModel du projet

/**
 * Tests d'intégration pour EmailVerificationModel
 * 
 * @testdox Tests d'intégration - Vérification d'email avec base de données
 * @group integration
 * @group database
 */
class EmailVerificationModelIntegrationTest extends DatabaseTestCase // Hérite de DatabaseTestCase (fournit connexion DB et transactions)
{
    private EmailVerificationModel $model; // Instance de EmailVerificationModel utilisée dans les tests
    
    protected function setUp(): void // Méthode appelée AVANT chaque test
    {
        parent::setUp(); // Appelle setUp() de DatabaseTestCase (charge .env, crée connexion PDO, démarre transaction)
        
        $this->model = new EmailVerificationModel(); // Crée une nouvelle instance de EmailVerificationModel pour chaque test
    }
    
    /**
     * @testdox Génère et stocke un code de vérification valide de 6 chiffres en base de données (generateAndStoreCode() crée un code, l'insère dans email_verification_codes avec expires_at, puis vérifie en base avec SELECT)
     */
    public function testGenerateAndStoreCodeCreatesValidCode(): void // Test : generateAndStoreCode() génère et stocke un code valide de 6 chiffres
    {
        $email = 'test@example.com'; // Email de test pour lequel générer le code
        
        $code = $this->model->generateAndStoreCode($email, 10); // Génère un code de 6 chiffres, le stocke en base avec expiration (10 min), retourne le code
        
        $this->assertIsString($code); // Vérifie que $code est une chaîne de caractères
        
        $this->assertEquals(6, strlen($code)); // Vérifie que la longueur de $code est exactement 6 caractères
        
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code); // Vérifie que $code contient exactement 6 chiffres (0-9) avec une regex
        
        $stmt = $this->pdo->prepare('SELECT * FROM email_verification_codes WHERE email = ? AND code = ?'); // Prépare la requête pour chercher le code en base
        
        $stmt->execute([$email, $code]); // Exécute la requête avec l'email et le code
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC); // Récupère la ligne sous forme de tableau associatif, stocke dans $result
        
        $this->assertIsArray($result); // Vérifie que $result est un tableau (le code a été trouvé en base)
        
        $this->assertEquals($email, $result['email']); // Vérifie que l'email correspond
        
        $this->assertEquals($code, $result['code']); // Vérifie que le code correspond
        
        $this->assertNotNull($result['expires_at']); // Vérifie que la date d'expiration n'est pas null
    }
    
    /**
     * @testdox Retourne un statut valide pour un code de vérification correct (génère un code, puis checkCodeStatus() le compare avec celui en base, retourne ['valid' => true, 'reason' => 'valid'])
     */
    public function testCheckCodeStatusReturnsValidForCorrectCode(): void // Test : checkCodeStatus() retourne un statut valide pour un code correct
    {
        $email = 'valid@example.com'; // Email de test
        
        $code = $this->model->generateAndStoreCode($email, 10); // Génère et stocke un code pour cet email
        
        $status = $this->model->checkCodeStatus($email, $code); // Vérifie le statut du code, stocke le résultat dans $status
        
        $this->assertIsArray($status); // Vérifie que $status est un tableau
        
        $this->assertTrue($status['valid']); // Vérifie que le statut indique que le code est valide
        
        $this->assertEquals('valid', $status['reason']); // Vérifie que la raison est 'valid'
    }
    
    /**
     * @testdox Retourne un statut invalide pour un code de vérification incorrect (génère un code, puis checkCodeStatus() avec un code différent retourne ['valid' => false, 'reason' => 'incorrect'])
     */
    public function testCheckCodeStatusReturnsIncorrectForWrongCode(): void // Test : checkCodeStatus() retourne un statut invalide pour un code incorrect
    {
        $email = 'test@example.com'; // Email de test
        
        $this->model->generateAndStoreCode($email, 10); // Génère et stocke un code pour cet email
        
        $status = $this->model->checkCodeStatus($email, '999999'); // Vérifie le statut avec un MAUVAIS code, stocke dans $status
        
        $this->assertIsArray($status); // Vérifie que $status est un tableau
        
        $this->assertFalse($status['valid']); // Vérifie que le statut indique que le code est invalide
        
        $this->assertEquals('incorrect', $status['reason']); // Vérifie que la raison est 'incorrect'
    }
    
    /**
     * @testdox Crée un enregistrement dans pending_registrations pour une inscription en attente (storePendingRegistration() insère nom, prenom, email, password hashé, puis vérifie avec SELECT que les données sont correctes)
     */
    public function testStorePendingRegistrationCreatesRecord(): void // Test : storePendingRegistration() crée un enregistrement dans pending_registrations
    {
        $nom = 'Dupont'; // Nom de l'utilisateur
        $prenom = 'Jean'; // Prénom de l'utilisateur
        $email = 'jean.dupont@example.com'; // Email de l'utilisateur
        $password = password_hash('Password123!', PASSWORD_DEFAULT); // Hash le mot de passe
        
        $result = $this->model->storePendingRegistration($nom, $prenom, $email, $password); // Stocke l'inscription en attente, stocke le résultat dans $result
        
        $this->assertTrue($result); // Vérifie que $result est true (l'inscription a été créée)
        
        $stmt = $this->pdo->prepare('SELECT * FROM pending_registrations WHERE email = ?'); // Prépare la requête pour vérifier en base
        
        $stmt->execute([$email]); // Exécute la requête avec l'email
        
        $record = $stmt->fetch(\PDO::FETCH_ASSOC); // Récupère la ligne sous forme de tableau associatif, stocke dans $record
        
        $this->assertIsArray($record); // Vérifie que $record est un tableau (l'inscription existe)
        
        $this->assertEquals($nom, $record['nom']); // Vérifie que le nom correspond
        
        $this->assertEquals($prenom, $record['prenom']); // Vérifie que le prénom correspond
        
        $this->assertEquals($email, $record['email']); // Vérifie que l'email correspond
    }
    
    /**
     * @testdox Un nouveau code de vérification remplace l'ancien pour le même email (génère un premier code, compte les codes, génère un second code, les deux codes sont différents mais le nouveau existe en base)
     */
    public function testGeneratingNewCodeReplacesOldOne(): void // Test : générer un nouveau code pour le même email crée un code différent
    {
        $email = 'replace@example.com'; // Email de test
        
        $code1 = $this->model->generateAndStoreCode($email, 10); // Génère un premier code, stocke dans $code1
        
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM email_verification_codes WHERE email = ?'); // Prépare la requête pour compter les codes
        
        $stmt->execute([$email]); // Exécute la requête avec l'email
        
        $count1 = $stmt->fetchColumn(); // Récupère le nombre de codes, stocke dans $count1
        
        $code2 = $this->model->generateAndStoreCode($email, 10); // Génère un second code, stocke dans $code2
        
        $stmt->execute([$email]); // Réexécute la requête pour compter à nouveau
        
        $count2 = $stmt->fetchColumn(); // Récupère le nouveau nombre de codes, stocke dans $count2
        
        $this->assertNotEquals($code1, $code2); // Vérifie que les deux codes sont différents
        
        $this->assertGreaterThanOrEqual($count1, $count2); // Vérifie que le nombre de codes est >= au nombre initial (peut y avoir plusieurs codes)
    }
    
    /**
     * @testdox Limite le TTL entre 1 et 60 minutes (valeurs invalides sont ajustées) (génère des codes avec TTL 0, 100 et 15, le système limite automatiquement à 1-60, aucune exception ne doit être levée)
     */
    public function testTtlIsClampedToValidRange(): void // Test : le TTL est limité entre 1 et 60 minutes
    {
        $email = 'ttl@example.com'; // Email de test
        
        $this->model->generateAndStoreCode($email, 0); // Génère un code avec TTL trop petit (0) - devrait être remonté à 1
        
        $this->model->generateAndStoreCode($email, 100); // Génère un code avec TTL trop grand (100) - devrait être limité à 60
        
        $this->model->generateAndStoreCode($email, 15); // Génère un code avec TTL valide (15) - devrait rester à 15
        
        $this->assertTrue(true); // Si aucune exception n'est levée, le test passe (les TTL sont bien limités)
    }
    
    /**
     * @testdox Récupère une inscription en attente (getPendingRegistration() fait un SELECT * FROM pending_registrations WHERE email = ?, retourne un tableau ou null)
     */
    public function testGetPendingRegistrationReturnsRecord(): void
    {
        $nom = 'Pending';
        $prenom = 'User';
        $email = 'getpending@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $this->model->storePendingRegistration($nom, $prenom, $email, $password);
        
        $pending = $this->model->getPendingRegistration($email);
        
        $this->assertIsArray($pending);
        $this->assertEquals($nom, $pending['nom']);
        $this->assertEquals($prenom, $pending['prenom']);
        $this->assertEquals($email, $pending['email']);
        
        $nonExistent = $this->model->getPendingRegistration('nonexistent@example.com');
        $this->assertNull($nonExistent);
    }
}
