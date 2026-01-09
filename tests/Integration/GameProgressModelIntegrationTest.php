<?php

namespace Tests\Integration;

use SAE_CyberCigales_G5\Modules\model\GameProgressModel;
use SAE_CyberCigales_G5\Modules\model\UserModel;

/**
 * Tests d'intégration pour GameProgressModel
 *
 * @testdox Tests d'intégration - Gestion de la progression du jeu
 * @group integration
 */
class GameProgressModelIntegrationTest extends DatabaseTestCase
{
    // Modèle de progression de jeu
    private GameProgressModel $model;

    // Modèle utilisateur
    private UserModel $userModel;

    // ID de l'utilisateur de test
    private int $testUserId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new GameProgressModel();
        $this->userModel = new UserModel();

        // Crée un utilisateur de test directement dans la table users
        $testEmail = 'gametest_' . bin2hex(random_bytes(4)) . '@test.com';
        $hashedPassword = password_hash('TestPassword123!', PASSWORD_DEFAULT);

        // Utilise ConnectionDB directement pour insérer l'utilisateur
        $db = \SAE_CyberCigales_G5\includes\ConnectionDB::getInstance();
        $db->insert('users', [
            'nom' => 'TestNom',
            'prenom' => 'TestPrenom',
            'email' => $testEmail,
            'password' => $hashedPassword,
            'email_verified' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Récupère l'utilisateur créé via l'authentification
        $user = $this->userModel->authenticate($testEmail, 'TestPassword123!');

        if ($user === null) {
            throw new \RuntimeException(
                "Impossible de créer l'utilisateur de test"
            );
        }

        $this->testUserId = (int) $user['id'];
    }

    /**
     * @testdox Crée une nouvelle progression de jeu avec une équipe et un niveau initial
     * (create() insère user_id, team, level = 1 dans game_progress, retourne true)
     */
    public function testCreateGameProgress(): void
    {
        // Crée une nouvelle progression pour l'utilisateur
        $result = $this->model->create($this->testUserId, 'butterfly');

        $this->assertTrue(
            $result,
            "Échec : create() devrait retourner true après insertion.\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php\n" .
            "Vérifiez que la méthode insère correctement dans game_progress."
        );

        // Vérifie que la progression a été créée en base
        $progress = $this->model->getByUserId($this->testUserId);

        $this->assertIsArray(
            $progress,
            "Échec : getByUserId() devrait retourner un tableau après création.\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php\n" .
            "La progression n'a pas été trouvée en base."
        );

        // Vérifie que la clé 'team' existe dans le résultat
        $this->assertArrayHasKey(
            'team',
            $progress,
            "Échec : La clé 'team' devrait exister dans le résultat.\n" .
            "Clés disponibles : " . implode(', ', array_keys($progress)) . "\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php\n" .
            "Vérifiez la structure de la table game_progress."
        );

        // Si la valeur team n'est pas vide, elle doit correspondre à celle fournie
        if ($progress['team'] !== '') {
            $this->assertEquals(
                'butterfly',
                $progress['team'],
                "Échec : L'équipe devrait être 'butterfly'.\n" .
                "Valeur attendue : 'butterfly'\n" .
                "Valeur obtenue : {$progress['team']}\n" .
                "Fichier : tests/Integration/GameProgressModelIntegrationTest.php ligne "
                . (__LINE__ - 7) . "\n" .
                "Vérifiez que create() stocke correctement le nom de l'équipe."
            );
        }

        // Vérifie que le niveau initial est bien égal à 1
        $this->assertEquals(
            1,
            (int) $progress['level'],
            "Échec : Le niveau initial devrait être 1.\n" .
            "Valeur attendue : 1\n" .
            "Valeur obtenue : {$progress['level']}\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php ligne "
            . (__LINE__ - 7) . "\n" .
            "Vérifiez que create() initialise le niveau à 1."
        );
    }

    /**
     * @testdox Récupère la progression d'un utilisateur par son ID
     * (getByUserId() fait un SELECT * FROM game_progress WHERE user_id = ?)
     */
    public function testGetByUserId(): void
    {
        // Crée une progression
        $this->model->create($this->testUserId, 'ladybug');

        // Récupère la progression
        $progress = $this->model->getByUserId($this->testUserId);

        $this->assertIsArray(
            $progress,
            "Échec : getByUserId() devrait retourner un tableau pour un utilisateur existant.\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php\n" .
            "Vérifiez que le SELECT trouve la progression."
        );

        // Vérifie que l'ID utilisateur correspond
        $this->assertEquals(
            $this->testUserId,
            (int) $progress['user_id'],
            "Échec : L'ID utilisateur devrait correspondre.\n" .
            "Valeur attendue : {$this->testUserId}\n" .
            "Valeur obtenue : {$progress['user_id']}\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php ligne "
            . (__LINE__ - 7) . "\n" .
            "Vérifiez que getByUserId() retourne la bonne progression."
        );
    }

    /**
     * @testdox Retourne null quand aucune progression n'existe pour l'utilisateur
     * (getByUserId() ne trouve rien, retourne null)
     */
    public function testGetByUserIdReturnsNullWhenNoProgress(): void
    {
        // Utilise un ID utilisateur qui n'a pas de progression
        $progress = $this->model->getByUserId(999999);

        $this->assertNull(
            $progress,
            "Échec : getByUserId() devrait retourner null quand aucune progression n'existe.\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php\n" .
            "Vérifiez que la méthode gère correctement l'absence de résultats."
        );
    }

    /**
     * @testdox Met à jour le niveau d'un utilisateur
     * (updateLevel() fait un UPDATE game_progress SET level = ? WHERE user_id = ?)
     */
    public function testUpdateLevel(): void
    {
        // Crée une progression au niveau 1
        $this->model->create($this->testUserId, 'bee');

        // Met à jour le niveau à 5
        $result = $this->model->updateLevel($this->testUserId, 5);

        $this->assertTrue(
            $result,
            "Échec : updateLevel() devrait retourner true après mise à jour.\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php\n" .
            "Vérifiez que l'UPDATE s'exécute correctement."
        );

        // Vérifie que le niveau a bien été mis à jour
        $progress = $this->model->getByUserId($this->testUserId);

        $this->assertEquals(
            5,
            $progress['level'],
            "Échec : Le niveau devrait être 5 après mise à jour.\n" .
            "Valeur attendue : 5\n" .
            "Valeur obtenue : {$progress['level']}\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php ligne "
            . (__LINE__ - 7) . "\n" .
            "Vérifiez que updateLevel() modifie correctement le niveau en base."
        );
    }

    /**
     * @testdox Récupère toutes les progressions de jeu
     * (getAllGameProgress() fait un SELECT * FROM game_progress)
     */
    public function testGetAllGameProgress(): void
    {
        // Crée une progression
        $this->model->create($this->testUserId, 'butterfly');

        // Récupère toutes les progressions
        $allProgress = $this->model->getAllGameProgress();

        $this->assertIsArray(
            $allProgress,
            "Échec : getAllGameProgress() devrait retourner un tableau.\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php\n" .
            "Vérifiez que le SELECT retourne bien un tableau."
        );

        $this->assertGreaterThanOrEqual(
            1,
            count($allProgress),
            "Échec : getAllGameProgress() devrait retourner au moins 1 progression.\n" .
            "Nombre attendu : >= 1\n" .
            "Nombre obtenu : " . count($allProgress) . "\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php ligne "
            . (__LINE__ - 7) . "\n" .
            "Vérifiez que la progression créée est bien récupérée."
        );

        // Vérifie que la progression de l'utilisateur de test est présente
        $found = false;

        foreach ($allProgress as $progress) {
            if ((int) $progress['user_id'] === $this->testUserId) {
                $found = true;
                break;
            }
        }

        $this->assertTrue(
            $found,
            "Échec : La progression de l'utilisateur test devrait être dans les résultats.\n" .
            "Fichier : tests/Integration/GameProgressModelIntegrationTest.php\n" .
            "Vérifiez que getAllGameProgress() retourne toutes les progressions."
        );
    }
}
