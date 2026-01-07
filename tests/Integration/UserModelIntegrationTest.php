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
    
    /**
     * @testdox Change le mot de passe d'un utilisateur (changePwd() hash le nouveau mot de passe avec password_hash() puis fait un UPDATE pour le modifier en base)
     */
    public function testChangePwdUpdatesPassword(): void
    {
        $email = 'changepwd@example.com';
        $oldPassword = password_hash('OldPass123!', PASSWORD_DEFAULT);
        $newPassword = 'NewPass456!';
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Change', 'Pwd', $email, $oldPassword]);
        
        $result = $this->model->changePwd($newPassword, $email);
        
        $this->assertTrue($result);
        
        $stmt = $this->pdo->prepare('SELECT password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $hashedPassword = $stmt->fetchColumn();
        
        $this->assertTrue(password_verify($newPassword, $hashedPassword));
    }
    
    /**
     * @testdox Supprime un utilisateur de la base de données (delete() fait un DELETE FROM users WHERE email = ?, retourne true si succès)
     */
    public function testDeleteRemovesUser(): void
    {
        $email = 'delete@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Delete', 'Test', $email, $password]);
        
        $result = $this->model->delete($email);
        
        $this->assertTrue($result);
        
        $exists = $this->model->findByEmail($email);
        $this->assertFalse($exists);
    }
    
    /**
     * @testdox Récupère tous les utilisateurs de la base de données (getAllUsers() fait un SELECT * FROM users, retourne un tableau d'utilisateurs)
     */
    public function testGetAllUsersReturnsArray(): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute(['User1', 'Test1', 'user1@example.com', password_hash('Pass123!', PASSWORD_DEFAULT)]);
        $stmt->execute(['User2', 'Test2', 'user2@example.com', password_hash('Pass123!', PASSWORD_DEFAULT)]);
        
        $users = $this->model->getAllUsers();
        
        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(2, count($users));
    }
    
    /**
     * @testdox Met à jour les informations d'un utilisateur (updateUser() fait un UPDATE users SET nom, prenom, email WHERE id = ?, retourne true si succès)
     */
    public function testUpdateUserModifiesRecord(): void
    {
        $email = 'update@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Old', 'Name', $email, $password]);
        
        $userId = (int)$this->pdo->lastInsertId();
        
        $result = $this->model->updateUser($userId, 'New', 'Name', 'newemail@example.com');
        
        $this->assertTrue($result);
        
        $user = $this->model->getUserById($userId);
        $this->assertEquals('New', $user['nom']);
        $this->assertEquals('Name', $user['prenom']);
        $this->assertEquals('newemail@example.com', $user['email']);
    }
    
    /**
     * @testdox Récupère un utilisateur par son ID (getUserById() fait un SELECT * FROM users WHERE id = ?, retourne un tableau ou null)
     */
    public function testGetUserByIdReturnsUser(): void
    {
        $email = 'getbyid@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute(['GetById', 'Test', $email, $password]);
        
        $userId = (int)$this->pdo->lastInsertId();
        
        $user = $this->model->getUserById($userId);
        
        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
        
        $nonExistent = $this->model->getUserById(999999);
        $this->assertNull($nonExistent);
    }
    
    /**
     * @testdox Bannit un utilisateur (banUser() fait un UPDATE users SET is_banned = 1 WHERE id = ?, retourne true si succès)
     */
    public function testBanUserSetsFlag(): void
    {
        $email = 'ban@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Ban', 'Test', $email, $password]);
        
        $userId = (int)$this->pdo->lastInsertId();
        
        $result = $this->model->banUser($userId);
        
        $this->assertTrue($result);
        
        $stmt = $this->pdo->prepare('SELECT is_banned FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $isBanned = $stmt->fetchColumn();
        
        $this->assertEquals(1, $isBanned);
    }
    
    /**
     * @testdox Débannit un utilisateur (unbanUser() fait un UPDATE users SET is_banned = 0 WHERE id = ?, retourne true si succès)
     */
    public function testUnbanUserClearsFlag(): void
    {
        $email = 'unban@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password, is_banned) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(['Unban', 'Test', $email, $password, 1]);
        
        $userId = (int)$this->pdo->lastInsertId();
        
        $result = $this->model->unbanUser($userId);
        
        $this->assertTrue($result);
        
        $stmt = $this->pdo->prepare('SELECT is_banned FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $isBanned = $stmt->fetchColumn();
        
        $this->assertEquals(0, $isBanned);
    }
    
    /**
     * @testdox Vérifie le statut d'un email (getEmailStatus() vérifie si l'email existe dans users ou pending_registrations, retourne un tableau avec exists, verified, pending)
     */
    public function testGetEmailStatusReturnsCorrectStatus(): void
    {
        $verifiedEmail = 'verified@example.com';
        $pendingEmail = 'pending@example.com';
        $nonExistentEmail = 'nonexistent@example.com';
        
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Verified', 'User', $verifiedEmail, $password]);
        
        $stmt = $this->pdo->prepare('INSERT INTO pending_registrations (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Pending', 'User', $pendingEmail, $password]);
        
        $verifiedStatus = $this->model->getEmailStatus($verifiedEmail);
        $this->assertTrue($verifiedStatus['exists']);
        $this->assertTrue($verifiedStatus['verified']);
        $this->assertFalse($verifiedStatus['pending']);
        
        $pendingStatus = $this->model->getEmailStatus($pendingEmail);
        $this->assertTrue($pendingStatus['exists']);
        $this->assertFalse($pendingStatus['verified']);
        $this->assertTrue($pendingStatus['pending']);
        
        $nonExistentStatus = $this->model->getEmailStatus($nonExistentEmail);
        $this->assertFalse($nonExistentStatus['exists']);
        $this->assertFalse($nonExistentStatus['verified']);
        $this->assertFalse($nonExistentStatus['pending']);
    }
}
