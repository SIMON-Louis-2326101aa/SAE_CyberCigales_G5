<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SAE_CyberCigales_G5\Modules\model\UserModel;
use SAE_CyberCigales_G5\Modules\model\PendingRegistrationModel;
use SAE_CyberCigales_G5\includes\ConnectionDB;

/**
 * Tests unitaires pour UserModel avec mocks
 * 
 * @testdox Tests unitaires - UserModel (avec mocks)
 * @group unit
 * @group mock
 */
class UserModelTest extends TestCase
{
    private ConnectionDB $dbMock;
    private PendingRegistrationModel $pendingMock;
    
    protected function setUp(): void // Méthode appelée AVANT chaque test
    {
        // Crée les mocks avant chaque test (faux objets pour simuler ConnectionDB et PendingRegistrationModel)
        $this->dbMock = $this->createMock(ConnectionDB::class); // Crée un mock de ConnectionDB (simule la base de données)
        $this->pendingMock = $this->createMock(PendingRegistrationModel::class); // Crée un mock de PendingRegistrationModel
    }
    
    /**
     * @testdox findByEmail() retourne true quand l'utilisateur existe (mock retourne un ID)
     */
    public function testFindByEmailReturnsTrueWhenUserExists(): void // Test : findByEmail() retourne true pour un utilisateur existant (avec mock)
    {
        // Configure le mock : getElement() retourne un ID (utilisateur existe)
        $this->dbMock->expects($this->once()) // Attend que la méthode soit appelée exactement 1 fois
                     ->method('getElement') // Spécifie quelle méthode sera appelée
                     ->with('users', 'id', ['email' => 'test@example.com']) // Vérifie les paramètres passés à getElement()
                     ->willReturn(1); // Le mock retourne 1 (ID de l'utilisateur) au lieu d'interroger la vraie base de données
        
        // Crée le modèle avec les mocks (injecte le mock au lieu de la vraie base de données)
        $model = new UserModel($this->dbMock, $this->pendingMock); // UserModel utilise le mock au lieu de ConnectionDB::getInstance()
        
        // Teste la méthode (appelle findByEmail() qui va utiliser le mock)
        $result = $model->findByEmail('test@example.com'); // findByEmail() appelle getElement() sur le mock
        
        // Vérifie le résultat (doit être true car le mock a retourné 1)
        $this->assertTrue($result); // Assertion : vérifie que $result est true
    }
    
    /**
     * @testdox findByEmail() retourne false quand l'utilisateur n'existe pas (mock retourne null)
     */
    public function testFindByEmailReturnsFalseWhenUserDoesNotExist(): void // Test : findByEmail() retourne false pour un utilisateur inexistant (avec mock)
    {
        // Configure le mock : getElement() retourne null (utilisateur n'existe pas)
        $this->dbMock->expects($this->once()) // Attend que la méthode soit appelée exactement 1 fois
                     ->method('getElement') // Spécifie quelle méthode sera appelée
                     ->with('users', 'id', ['email' => 'nonexistent@example.com']) // Vérifie les paramètres passés
                     ->willReturn(null); // Le mock retourne null (utilisateur non trouvé) au lieu d'interroger la vraie base de données
        
        // Crée le modèle avec les mocks
        $model = new UserModel($this->dbMock, $this->pendingMock); // UserModel utilise le mock au lieu de ConnectionDB::getInstance()
        
        // Teste la méthode
        $result = $model->findByEmail('nonexistent@example.com'); // findByEmail() appelle getElement() sur le mock qui retourne null
        
        // Vérifie le résultat (doit être false car le mock a retourné null)
        $this->assertFalse($result); // Assertion : vérifie que $result est false
    }
    
    /**
     * @testdox emailExists() retourne true quand l'email existe (appelle findByEmail() qui retourne true)
     */
    public function testEmailExistsReturnsTrueWhenEmailExists(): void // Test : emailExists() retourne true quand l'email existe (appelle findByEmail() en interne)
    {
        // Configure le mock (emailExists() appelle findByEmail() qui appelle getElement())
        $this->dbMock->expects($this->once()) // Attend que getElement() soit appelé exactement 1 fois
                     ->method('getElement') // Spécifie quelle méthode sera appelée
                     ->willReturn(1); // Le mock retourne 1 (ID trouvé = email existe)
        
        // Crée le modèle avec les mocks
        $model = new UserModel($this->dbMock, $this->pendingMock); // UserModel utilise le mock
        
        // Teste la méthode (emailExists() appelle findByEmail() qui appelle getElement() sur le mock)
        $result = $model->emailExists('exists@example.com'); // emailExists() retourne le résultat de findByEmail()
        
        // Vérifie le résultat (doit être true car le mock a retourné 1)
        $this->assertTrue($result); // Assertion : vérifie que $result est true
    }
    
    /**
     * @testdox emailExists() retourne false quand l'email n'existe pas
     */
    public function testEmailExistsReturnsFalseWhenEmailDoesNotExist(): void // Test : emailExists() retourne false quand l'email n'existe pas
    {
        // Configure le mock
        $this->dbMock->expects($this->once()) // Attend que getElement() soit appelé exactement 1 fois
                     ->method('getElement') // Spécifie quelle méthode sera appelée
                     ->willReturn(null); // Le mock retourne null (pas d'ID trouvé = email n'existe pas)
        
        // Crée le modèle avec les mocks
        $model = new UserModel($this->dbMock, $this->pendingMock); // UserModel utilise le mock
        
        // Teste la méthode
        $result = $model->emailExists('nonexistent@example.com'); // emailExists() appelle findByEmail() qui retourne false car getElement() retourne null
        
        // Vérifie le résultat (doit être false car le mock a retourné null)
        $this->assertFalse($result); // Assertion : vérifie que $result est false
    }
    
    /**
     * @testdox authenticate() retourne l'utilisateur avec le bon mot de passe (mock retourne un user avec password_hash valide)
     */
    public function testAuthenticateSucceedsWithCorrectPassword(): void // Test : authenticate() réussit avec le bon mot de passe (mock retourne un user avec password_hash valide)
    {
        $email = 'auth@example.com'; // Email de test
        $password = 'MyPassword123!'; // Mot de passe en clair (sera utilisé pour l'authentification)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash le mot de passe comme s'il était stocké en base
        
        // Configure le mock : getAll() retourne un utilisateur avec le password hashé
        $this->dbMock->expects($this->once()) // Attend que getAll() soit appelé exactement 1 fois
                     ->method('getAll') // Spécifie quelle méthode sera appelée
                     ->with('users', ['email' => $email], 1) // Vérifie les paramètres : table users, filtre par email, limite 1
                     ->willReturn([ // Le mock retourne un tableau avec 1 utilisateur (simule le résultat SQL)
                         [
                             'id' => 1, // ID de l'utilisateur
                             'nom' => 'Test', // Nom de test
                             'prenom' => 'User', // Prénom de test
                             'email' => $email, // Email de test
                             'password' => $hashedPassword // Password hashé (password_verify() va le comparer avec $password)
                         ]
                     ]);
        
        // Crée le modèle avec les mocks
        $model = new UserModel($this->dbMock, $this->pendingMock); // UserModel utilise le mock au lieu de la vraie base de données
        
        // Teste la méthode (authenticate() va appeler getAll() sur le mock puis password_verify() sur le hash)
        $user = $model->authenticate($email, $password); // authenticate() récupère le user du mock et vérifie le mot de passe avec password_verify()
        
        // Vérifie le résultat (doit retourner le tableau utilisateur car password_verify() retourne true)
        $this->assertIsArray($user); // Assertion : vérifie que $user est un tableau (authentification réussie)
        $this->assertEquals($email, $user['email']); // Assertion : vérifie que l'email correspond (bon utilisateur retourné)
    }
    
    /**
     * @testdox authenticate() retourne null avec un mauvais mot de passe
     */
    public function testAuthenticateFailsWithWrongPassword(): void // Test : authenticate() retourne null avec un mauvais mot de passe
    {
        $email = 'auth@example.com'; // Email de test
        $correctPassword = 'CorrectPass123!'; // BON mot de passe (sera hashé et stocké)
        $wrongPassword = 'WrongPass123!'; // MAUVAIS mot de passe (sera utilisé pour l'authentification, devrait échouer)
        $hashedPassword = password_hash($correctPassword, PASSWORD_DEFAULT); // Hash le BON mot de passe
        
        // Configure le mock
        $this->dbMock->expects($this->once()) // Attend que getAll() soit appelé exactement 1 fois
                     ->method('getAll') // Spécifie quelle méthode sera appelée
                     ->willReturn([ // Le mock retourne un utilisateur avec le hash du BON mot de passe
                         [
                             'id' => 1, // ID de l'utilisateur
                             'email' => $email, // Email de test
                             'password' => $hashedPassword // Hash du BON mot de passe (password_verify() va comparer avec le MAUVAIS)
                         ]
                     ]);
        
        // Crée le modèle avec les mocks
        $model = new UserModel($this->dbMock, $this->pendingMock); // UserModel utilise le mock
        
        // Teste la méthode avec le MAUVAIS mot de passe
        $user = $model->authenticate($email, $wrongPassword); // authenticate() récupère le user et appelle password_verify($wrongPassword, $hashedPassword) qui retourne false
        
        // Vérifie le résultat (doit être null car password_verify() retourne false)
        $this->assertNull($user); // Assertion : vérifie que $user est null (authentification échouée)
    }
    
    /**
     * @testdox authenticate() retourne null quand l'utilisateur n'existe pas
     */
    public function testAuthenticateFailsWhenUserDoesNotExist(): void // Test : authenticate() retourne null quand l'utilisateur n'existe pas
    {
        // Configure le mock : getAll() retourne un tableau vide
        $this->dbMock->expects($this->once()) // Attend que getAll() soit appelé exactement 1 fois
                     ->method('getAll') // Spécifie quelle méthode sera appelée
                     ->willReturn([]); // Le mock retourne un tableau vide (aucun utilisateur trouvé)
        
        // Crée le modèle avec les mocks
        $model = new UserModel($this->dbMock, $this->pendingMock); // UserModel utilise le mock
        
        // Teste la méthode
        $user = $model->authenticate('nonexistent@example.com', 'Password123!'); // authenticate() appelle getAll() qui retourne [], donc $user = null
        
        // Vérifie le résultat (doit être null car aucun utilisateur n'a été trouvé)
        $this->assertNull($user); // Assertion : vérifie que $user est null (authentification échouée car utilisateur inexistant)
    }
}
