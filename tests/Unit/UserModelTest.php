<?php

// Balise d'ouverture PHP

namespace Tests\Unit;

// Namespace des tests unitaires

use PHPUnit\Framework\TestCase;
use SAE_CyberCigales_G5\Modules\model\UserModel;
use SAE_CyberCigales_G5\Modules\model\PendingRegistrationModel;
use SAE_CyberCigales_G5\includes\ConnectionDB;

// Dépendances mockées

/**
 * Tests unitaires pour UserModel avec mocks
 *
 * @testdox Tests unitaires - UserModel (avec mocks)
 * @group unit
 * @group mock
 */
class UserModelTest extends TestCase
{
    // Mock de la connexion à la base de données
    private ConnectionDB $dbMock;

    // Mock du modèle PendingRegistrationModel
    private PendingRegistrationModel $pendingMock;

    protected function setUp(): void
    {
        // Création des mocks avant chaque test
        $this->dbMock = $this->createMock(ConnectionDB::class);
        $this->pendingMock = $this->createMock(PendingRegistrationModel::class);
    }

    /**
     * @testdox findByEmail() retourne true quand l'utilisateur existe
     */
    public function testFindByEmailReturnsTrueWhenUserExists(): void
    {
        // Configuration du mock : utilisateur existant
        $this->dbMock
            ->expects($this->once())
            ->method('getElement')
            ->with('users', 'id', ['email' => 'test@example.com'])
            ->willReturn(1);

        // Création du modèle avec les mocks
        $model = new UserModel($this->dbMock, $this->pendingMock);

        // Appel de la méthode testée
        $result = $model->findByEmail('test@example.com');

        $this->assertTrue($result);
    }

    /**
     * @testdox findByEmail() retourne false quand l'utilisateur n'existe pas
     */
    public function testFindByEmailReturnsFalseWhenUserDoesNotExist(): void
    {
        // Configuration du mock : utilisateur inexistant
        $this->dbMock
            ->expects($this->once())
            ->method('getElement')
            ->with('users', 'id', ['email' => 'nonexistent@example.com'])
            ->willReturn(null);

        $model = new UserModel($this->dbMock, $this->pendingMock);

        $result = $model->findByEmail('nonexistent@example.com');

        $this->assertFalse($result);
    }

    /**
     * @testdox emailExists() retourne true quand l'email existe
     */
    public function testEmailExistsReturnsTrueWhenEmailExists(): void
    {
        // Configuration du mock : email existant
        $this->dbMock
            ->expects($this->once())
            ->method('getElement')
            ->willReturn(1);

        $model = new UserModel($this->dbMock, $this->pendingMock);

        $result = $model->emailExists('exists@example.com');

        $this->assertTrue($result);
    }

    /**
     * @testdox emailExists() retourne false quand l'email n'existe pas
     */
    public function testEmailExistsReturnsFalseWhenEmailDoesNotExist(): void
    {
        // Configuration du mock : email inexistant
        $this->dbMock
            ->expects($this->once())
            ->method('getElement')
            ->willReturn(null);

        $model = new UserModel($this->dbMock, $this->pendingMock);

        $result = $model->emailExists('nonexistent@example.com');

        $this->assertFalse($result);
    }

    /**
     * @testdox authenticate() retourne l'utilisateur avec le bon mot de passe
     */
    public function testAuthenticateSucceedsWithCorrectPassword(): void
    {
        $email = 'auth@example.com';
        $password = 'MyPassword123!';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Configuration du mock : utilisateur trouvé avec mot de passe hashé
        $this->dbMock
            ->expects($this->once())
            ->method('getAll')
            ->with('users', ['email' => $email], 1)
            ->willReturn([
                [
                    'id' => 1,
                    'nom' => 'Test',
                    'prenom' => 'User',
                    'email' => $email,
                    'password' => $hashedPassword,
                ],
            ]);

        $model = new UserModel($this->dbMock, $this->pendingMock);

        $user = $model->authenticate($email, $password);

        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
    }

    /**
     * @testdox authenticate() retourne null avec un mauvais mot de passe
     */
    public function testAuthenticateFailsWithWrongPassword(): void
    {
        $email = 'auth@example.com';
        $correctPassword = 'CorrectPass123!';
        $wrongPassword = 'WrongPass123!';
        $hashedPassword = password_hash($correctPassword, PASSWORD_DEFAULT);

        // Configuration du mock : utilisateur trouvé mais mauvais mot de passe
        $this->dbMock
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                [
                    'id' => 1,
                    'email' => $email,
                    'password' => $hashedPassword,
                ],
            ]);

        $model = new UserModel($this->dbMock, $this->pendingMock);

        $user = $model->authenticate($email, $wrongPassword);

        $this->assertNull($user);
    }

    /**
     * @testdox authenticate() retourne null quand l'utilisateur n'existe pas
     */
    public function testAuthenticateFailsWhenUserDoesNotExist(): void
    {
        // Configuration du mock : aucun utilisateur trouvé
        $this->dbMock
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $model = new UserModel($this->dbMock, $this->pendingMock);

        $user = $model->authenticate('nonexistent@example.com', 'Password123!');

        $this->assertNull($user);
    }
}
