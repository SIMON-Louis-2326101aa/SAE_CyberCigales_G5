<?php

namespace Tests\Integration;

use SAE_CyberCigales_G5\Modules\model\PendingRegistrationModel;

/**
 * Tests d'intégration pour PendingRegistrationModel
 * 
 * @testdox Tests d'intégration - Gestion des inscriptions en attente
 * @group integration
 */
class PendingRegistrationModelIntegrationTest extends DatabaseTestCase
{
    private PendingRegistrationModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new PendingRegistrationModel();
    }

    /**
     * @testdox Récupère toutes les inscriptions en attente (getAllPendingRegistrations() fait un SELECT * FROM pending_registrations, retourne un tableau)
     */
    public function testGetAllPendingRegistrations(): void
    {
        // Crée plusieurs inscriptions en attente
        $email1 = 'pending1_' . bin2hex(random_bytes(4)) . '@test.com';
        $email2 = 'pending2_' . bin2hex(random_bytes(4)) . '@test.com';
        
        $this->model->storePendingRegistration(
            'Nom1',
            'Prenom1',
            $email1,
            password_hash('Password123!', PASSWORD_DEFAULT)
        );
        
        $this->model->storePendingRegistration(
            'Nom2',
            'Prenom2',
            $email2,
            password_hash('Password456!', PASSWORD_DEFAULT)
        );
        
        // Récupère toutes les inscriptions
        $allPending = $this->model->getAllPendingRegistrations();
        
        $this->assertIsArray(
            $allPending,
            "Échec : getAllPendingRegistrations() devrait retourner un tableau.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que le SELECT retourne bien un tableau."
        );
        
        $this->assertGreaterThanOrEqual(
            2,
            count($allPending),
            "Échec : getAllPendingRegistrations() devrait retourner au moins 2 inscriptions.\n" .
            "Nombre attendu : >= 2\n" .
            "Nombre obtenu : " . count($allPending) . "\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php ligne " . (__LINE__ - 6) . "\n" .
            "Vérifiez que toutes les inscriptions créées sont récupérées."
        );
        
        // Vérifie que nos inscriptions sont dans les résultats
        $emails = array_column($allPending, 'email');
        $this->assertContains(
            $email1,
            $emails,
            "Échec : L'email '$email1' devrait être dans les résultats.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que getAllPendingRegistrations() retourne toutes les inscriptions."
        );
        
        $this->assertContains(
            $email2,
            $emails,
            "Échec : L'email '$email2' devrait être dans les résultats.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que getAllPendingRegistrations() retourne toutes les inscriptions."
        );
    }

    /**
     * @testdox Récupère une inscription en attente par son ID (getPendingRegistrationById() fait un SELECT * FROM pending_registrations WHERE id = ?, retourne un tableau ou null)
     */
    public function testGetPendingRegistrationById(): void
    {
        // Crée une inscription en attente
        $email = 'pendingbyid_' . bin2hex(random_bytes(4)) . '@test.com';
        
        $this->model->storePendingRegistration(
            'NomTest',
            'PrenomTest',
            $email,
            password_hash('Password123!', PASSWORD_DEFAULT)
        );
        
        // Récupère l'inscription pour obtenir son ID
        $pending = $this->model->getPendingRegistration($email);
        $this->assertIsArray($pending, "L'inscription devrait exister");
        
        $pendingId = (int)$pending['id'];
        
        // Récupère l'inscription par son ID
        $result = $this->model->getPendingRegistrationById($pendingId);
        
        $this->assertIsArray(
            $result,
            "Échec : getPendingRegistrationById() devrait retourner un tableau pour un ID existant.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que le SELECT trouve l'inscription."
        );
        
        $this->assertEquals(
            $email,
            $result['email'],
            "Échec : L'email devrait correspondre.\n" .
            "Valeur attendue : $email\n" .
            "Valeur obtenue : {$result['email']}\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php ligne " . (__LINE__ - 6) . "\n" .
            "Vérifiez que getPendingRegistrationById() retourne la bonne inscription."
        );
        
        $this->assertEquals(
            'NomTest',
            $result['nom'],
            "Échec : Le nom devrait être 'NomTest'.\n" .
            "Valeur attendue : 'NomTest'\n" .
            "Valeur obtenue : {$result['nom']}\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php ligne " . (__LINE__ - 6) . "\n" .
            "Vérifiez que les données sont correctement stockées."
        );
        
        $this->assertEquals(
            'PrenomTest',
            $result['prenom'],
            "Échec : Le prénom devrait être 'PrenomTest'.\n" .
            "Valeur attendue : 'PrenomTest'\n" .
            "Valeur obtenue : {$result['prenom']}\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php ligne " . (__LINE__ - 6) . "\n" .
            "Vérifiez que les données sont correctement stockées."
        );
    }

    /**
     * @testdox Retourne null pour un ID d'inscription inexistant (getPendingRegistrationById() ne trouve rien, retourne null)
     */
    public function testGetPendingRegistrationByIdReturnsNullForNonExistent(): void
    {
        // Utilise un ID qui n'existe pas
        $result = $this->model->getPendingRegistrationById(999999);
        
        $this->assertNull(
            $result,
            "Échec : getPendingRegistrationById() devrait retourner null pour un ID inexistant.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que la méthode gère correctement l'absence de résultats."
        );
    }

    /**
     * @testdox Supprime une inscription en attente par son ID (deletePendingRegistrationById() fait un DELETE FROM pending_registrations WHERE id = ?, retourne true si succès)
     */
    public function testDeletePendingRegistrationById(): void
    {
        // Crée une inscription en attente
        $email = 'pendingdelete_' . bin2hex(random_bytes(4)) . '@test.com';
        
        $this->model->storePendingRegistration(
            'NomDelete',
            'PrenomDelete',
            $email,
            password_hash('Password123!', PASSWORD_DEFAULT)
        );
        
        // Récupère l'inscription pour obtenir son ID
        $pending = $this->model->getPendingRegistration($email);
        $this->assertIsArray($pending, "L'inscription devrait exister");
        
        $pendingId = (int)$pending['id'];
        
        // Supprime l'inscription
        $result = $this->model->deletePendingRegistrationById($pendingId);
        
        $this->assertTrue(
            $result,
            "Échec : deletePendingRegistrationById() devrait retourner true après suppression.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que le DELETE s'exécute correctement."
        );
        
        // Vérifie que l'inscription a bien été supprimée
        $deleted = $this->model->getPendingRegistrationById($pendingId);
        
        $this->assertNull(
            $deleted,
            "Échec : L'inscription devrait être null après suppression.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que deletePendingRegistrationById() supprime bien l'inscription."
        );
    }

    /**
     * @testdox Retourne false lors de la suppression d'une inscription inexistante (deletePendingRegistrationById() avec un ID invalide retourne false)
     */
    public function testDeletePendingRegistrationByIdReturnsFalseForNonExistent(): void
    {
        // Tente de supprimer un ID qui n'existe pas
        $result = $this->model->deletePendingRegistrationById(999999);
        
        $this->assertFalse(
            $result,
            "Échec : deletePendingRegistrationById() devrait retourner false pour un ID inexistant.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que la méthode gère correctement l'absence de ligne à supprimer."
        );
    }
}

