<?php

// Balise d'ouverture PHP

namespace Tests\Integration;

// Namespace des tests d'intégration

use SAE_CyberCigales_G5\Modules\model\UserModel;

// Modèle testé

/**
 * Tests d'intégration pour UserModel
 *
 * @testdox Tests d'intégration - Gestion des utilisateurs
 * @group integration
 * @group database
 */
class UserModelIntegrationTest extends DatabaseTestCase
{
    // Instance du modèle utilisé dans les tests
    private UserModel $model;

    protected function setUp(): void
    {
        // Initialise la base de données de test (transaction, PDO, etc.)
        parent::setUp();

        // Instancie le modèle à tester
        $this->model = new UserModel();
    }

    /**
     * @testdox Retourne true lorsqu'un utilisateur existe avec l'email donné
     */
    public function testFindByEmailReturnsTrueWhenExists(): void
    {
        // Email et mot de passe de test
        $email = 'testuser@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère un utilisateur en base
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Test', 'User', $email, $password]);

        // Vérifie que l'utilisateur est trouvé
        $result = $this->model->findByEmail($email);

        $this->assertTrue($result);
    }

    /**
     * @testdox Retourne false lorsqu'aucun utilisateur n'existe avec l'email donné
     */
    public function testFindByEmailReturnsFalseWhenNotExists(): void
    {
        // Recherche d'un email inexistant
        $result = $this->model->findByEmail('nonexistent@example.com');

        $this->assertFalse($result);
    }

    /**
     * @testdox Crée un utilisateur depuis pending_registrations après vérification
     */
    public function testCreateUserAfterVerificationInsertsNewRecord(): void
    {
        // Données de l'utilisateur
        $nom = 'Nouveau';
        $prenom = 'Utilisateur';
        $email = 'nouveau@example.com';
        $password = password_hash('SecurePass123!', PASSWORD_DEFAULT);

        // Insère une inscription en attente
        $stmt = $this->pdo->prepare(
            'INSERT INTO pending_registrations (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$nom, $prenom, $email, $password]);

        // Crée l'utilisateur définitif
        $result = $this->model->createUserAfterVerification($email);

        $this->assertTrue($result);

        // Vérifie que l'utilisateur existe en base
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
    }

    /**
     * @testdox L'authentification réussit avec le bon mot de passe
     */
    public function testAuthenticationSucceedsWithCorrectPassword(): void
    {
        $email = 'auth@example.com';
        $plainPassword = 'MyPassword123!';
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Auth', 'Test', $email, $hash]);

        // Authentification
        $user = $this->model->authenticate($email, $plainPassword);

        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
    }

    /**
     * @testdox L'authentification échoue avec un mauvais mot de passe
     */
    public function testAuthenticationFailsWithWrongPassword(): void
    {
        $email = 'wrongauth@example.com';
        $correctPassword = 'CorrectPass123!';
        $wrongPassword = 'WrongPass123!';
        $hash = password_hash($correctPassword, PASSWORD_DEFAULT);

        // Insère l'utilisateur avec le bon mot de passe
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Wrong', 'Auth', $email, $hash]);

        // Tentative d'authentification incorrecte
        $user = $this->model->authenticate($email, $wrongPassword);

        $this->assertEmpty($user);
    }

    /**
     * @testdox Vérifie l'existence d'un email
     */
    public function testEmailExistsReturnsTrueForExistingEmail(): void
    {
        $email = 'exists@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Exists', 'Test', $email, $password]);

        // Vérifications
        $this->assertTrue($this->model->emailExists($email));
        $this->assertFalse($this->model->emailExists('notexists@example.com'));
    }

    /**
     * @testdox Met à jour le mot de passe d'un utilisateur
     */
    public function testChangePwdUpdatesPassword(): void
    {
        $email = 'changepwd@example.com';
        $oldHash = password_hash('OldPass123!', PASSWORD_DEFAULT);
        $newPassword = 'NewPass456!';

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Change', 'Pwd', $email, $oldHash]);

        // Mise à jour du mot de passe
        $result = $this->model->changePwd($newPassword, $email);

        $this->assertTrue($result);

        // Vérifie le nouveau hash
        $stmt = $this->pdo->prepare('SELECT password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $hashedPassword = $stmt->fetchColumn();

        $this->assertTrue(password_verify($newPassword, $hashedPassword));
    }

    /**
     * @testdox Supprime un utilisateur
     */
    public function testDeleteRemovesUser(): void
    {
        $email = 'delete@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Delete', 'Test', $email, $password]);

        // Suppression
        $this->assertTrue($this->model->delete($email));
        $this->assertFalse($this->model->findByEmail($email));
    }

    /**
     * @testdox Récupère tous les utilisateurs
     */
    public function testGetAllUsersReturnsArray(): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );

        $stmt->execute(['User1', 'Test1', 'user1@example.com', password_hash('Pass123!', PASSWORD_DEFAULT)]);
        $stmt->execute(['User2', 'Test2', 'user2@example.com', password_hash('Pass123!', PASSWORD_DEFAULT)]);

        $users = $this->model->getAllUsers();

        $this->assertIsArray($users);
        $this->assertGreaterThanOrEqual(2, count($users));
    }

    /**
     * @testdox Met à jour les informations d'un utilisateur
     */
    public function testUpdateUserModifiesRecord(): void
    {
        $email = 'update@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Old', 'Name', $email, $password]);

        $userId = (int) $this->pdo->lastInsertId();

        // Mise à jour
        $this->assertTrue(
            $this->model->updateUser($userId, 'New', 'Name', 'newemail@example.com')
        );

        $user = $this->model->getUserById($userId);

        $this->assertEquals('New', $user['nom']);
        $this->assertEquals('Name', $user['prenom']);
        $this->assertEquals('newemail@example.com', $user['email']);
    }

    /**
     * @testdox Récupère un utilisateur par son ID
     */
    public function testGetUserByIdReturnsUser(): void
    {
        $email = 'getbyid@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['GetById', 'Test', $email, $password]);

        $userId = (int) $this->pdo->lastInsertId();

        $this->assertIsArray($this->model->getUserById($userId));
        $this->assertNull($this->model->getUserById(999999));
    }

    /**
     * @testdox Bannit un utilisateur
     */
    public function testBanUserSetsFlag(): void
    {
        $email = 'ban@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Ban', 'Test', $email, $password]);

        $userId = (int) $this->pdo->lastInsertId();

        $this->assertTrue($this->model->banUser($userId));

        $stmt = $this->pdo->prepare('SELECT is_banned FROM users WHERE id = ?');
        $stmt->execute([$userId]);

        $this->assertEquals(1, $stmt->fetchColumn());
    }

    /**
     * @testdox Débannit un utilisateur
     */
    public function testUnbanUserClearsFlag(): void
    {
        $email = 'unban@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Insère l'utilisateur banni
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password, is_banned) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute(['Unban', 'Test', $email, $password, 1]);

        $userId = (int) $this->pdo->lastInsertId();

        $this->assertTrue($this->model->unbanUser($userId));

        $stmt = $this->pdo->prepare('SELECT is_banned FROM users WHERE id = ?');
        $stmt->execute([$userId]);

        $this->assertEquals(0, $stmt->fetchColumn());
    }

    /**
     * @testdox Vérifie le statut d'un email
     */
    public function testGetEmailStatusReturnsCorrectStatus(): void
    {
        $verifiedEmail = 'verified@example.com';
        $pendingEmail = 'pending@example.com';
        $nonExistentEmail = 'nonexistent@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);

        // Utilisateur vérifié
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Verified', 'User', $verifiedEmail, $password]);

        // Utilisateur en attente
        $stmt = $this->pdo->prepare(
            'INSERT INTO pending_registrations (nom, prenom, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute(['Pending', 'User', $pendingEmail, $password]);

        $verifiedStatus = $this->model->getEmailStatus($verifiedEmail);
        $pendingStatus = $this->model->getEmailStatus($pendingEmail);
        $nonExistentStatus = $this->model->getEmailStatus($nonExistentEmail);

        $this->assertTrue($verifiedStatus['exists']);
        $this->assertTrue($verifiedStatus['verified']);
        $this->assertFalse($verifiedStatus['pending']);

        $this->assertTrue($pendingStatus['exists']);
        $this->assertFalse($pendingStatus['verified']);
        $this->assertTrue($pendingStatus['pending']);

        $this->assertFalse($nonExistentStatus['exists']);
        $this->assertFalse($nonExistentStatus['verified']);
        $this->assertFalse($nonExistentStatus['pending']);
    }
}
