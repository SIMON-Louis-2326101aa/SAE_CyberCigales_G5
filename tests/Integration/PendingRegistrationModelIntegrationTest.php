<?php

// Balise d'ouverture PHP

namespace Tests\Integration;

// Namespace des tests d'intégration

use SAE_CyberCigales_G5\Modules\model\PendingRegistrationModel;

// Modèle testé

/**
 * Tests d'intégration pour PendingRegistrationModel
 *
 * @testdox Tests d'intégration - Gestion des inscriptions en attente
 * @group integration
 */
class PendingRegistrationModelIntegrationTest extends DatabaseTestCase
{
    // Instance du modèle utilisé dans les tests
    private PendingRegistrationModel $model;

    protected function setUp(): void
    {
        // Initialise la base de données de test (transaction, PDO, etc.)
        parent::setUp();

        // Instancie le modèle à tester
        $this->model = new PendingRegistrationModel();
    }

    /**
     * @testdox Récupère toutes les inscriptions en attente
     */
    public function testGetAllPendingRegistrations(): void
    {
        // Génère des emails uniques pour éviter les collisions
        $email1 = 'pending1_' . bin2hex(random_bytes(4)) . '@test.com';
        $email2 = 'pending2_' . bin2hex(random_bytes(4)) . '@test.com';

        // Crée la première inscription en attente
        $this->model->storePendingRegistration(
            'Nom1',
            'Prenom1',
            $email1,
            password_hash('Password123!', PASSWORD_DEFAULT)
        );

        // Crée la seconde inscription en attente
        $this->model->storePendingRegistration(
            'Nom2',
            'Prenom2',
            $email2,
            password_hash('Password456!', PASSWORD_DEFAULT)
        );

        // Récupère toutes les inscriptions en attente
        $allPending = $this->model->getAllPendingRegistrations();

        // Vérifie que le résultat est un tableau
        $this->assertIsArray(
            $allPending,
            "Échec : getAllPendingRegistrations() devrait retourner un tableau.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que le SELECT retourne bien un tableau."
        );

        // Vérifie qu'au moins deux inscriptions sont retournées
        $this->assertGreaterThanOrEqual(
            2,
            count($allPending),
            "Échec : getAllPendingRegistrations() devrait retourner au moins 2 inscriptions.\n" .
            "Nombre attendu : >= 2\n" .
            "Nombre obtenu : " . count($allPending) . "\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que toutes les inscriptions créées sont récupérées."
        );

        // Extrait les emails retournés
        $emails = array_column($allPending, 'email');

        // Vérifie la présence du premier email
        $this->assertContains(
            $email1,
            $emails,
            "Échec : L'email '$email1' devrait être dans les résultats.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que toutes les inscriptions sont retournées."
        );

        // Vérifie la présence du second email
        $this->assertContains(
            $email2,
            $emails,
            "Échec : L'email '$email2' devrait être dans les résultats.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que toutes les inscriptions sont retournées."
        );
    }

    /**
     * @testdox Récupère une inscription en attente par son ID
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

        $pendingId = (int) $pending['id'];

        // Récupère l'inscription par son ID
        $result = $this->model->getPendingRegistrationById($pendingId);

        // Vérifie que l'inscription est trouvée
        $this->assertIsArray(
            $result,
            "Échec : getPendingRegistrationById() devrait retourner un tableau.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez que le SELECT fonctionne correctement."
        );

        // Vérifie les données retournées
        $this->assertEquals($email, $result['email']);
        $this->assertEquals('NomTest', $result['nom']);
        $this->assertEquals('PrenomTest', $result['prenom']);
    }

    /**
     * @testdox Retourne null pour un ID inexistant
     */
    public function testGetPendingRegistrationByIdReturnsNullForNonExistent(): void
    {
        // Utilise un ID inexistant
        $result = $this->model->getPendingRegistrationById(999999);

        // Vérifie que la méthode retourne null
        $this->assertNull(
            $result,
            "Échec : getPendingRegistrationById() devrait retourner null.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php\n" .
            "Vérifiez la gestion des résultats vides."
        );
    }

    /**
     * @testdox Supprime une inscription en attente par son ID
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

        $pendingId = (int) $pending['id'];

        // Supprime l'inscription
        $result = $this->model->deletePendingRegistrationById($pendingId);

        // Vérifie que la suppression a réussi
        $this->assertTrue(
            $result,
            "Échec : deletePendingRegistrationById() devrait retourner true.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php"
        );

        // Vérifie que l'inscription n'existe plus
        $deleted = $this->model->getPendingRegistrationById($pendingId);

        $this->assertNull(
            $deleted,
            "Échec : L'inscription devrait être supprimée.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php"
        );
    }

    /**
     * @testdox Retourne false lors de la suppression d'une inscription inexistante
     */
    public function testDeletePendingRegistrationByIdReturnsFalseForNonExistent(): void
    {
        // Tente de supprimer un ID inexistant
        $result = $this->model->deletePendingRegistrationById(999999);

        // Vérifie que la suppression échoue
        $this->assertFalse(
            $result,
            "Échec : deletePendingRegistrationById() devrait retourner false.\n" .
            "Fichier : tests/Integration/PendingRegistrationModelIntegrationTest.php"
        );
    }
}
