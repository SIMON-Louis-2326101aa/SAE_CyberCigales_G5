<?php

/**
 * Tests d'intégration pour LoginAttemptModel
 * Teste le système de rate limiting et de blocage progressif
 * 
 * Note: Ces tests nécessitent l'utilisation de sessions PHP.
 * Chaque test est exécuté dans un processus séparé pour éviter les conflits.
 */

namespace SAE_CyberCigales_G5\tests\Integration;

use PHPUnit\Framework\TestCase;
use SAE_CyberCigales_G5\Modules\model\LoginAttemptModel;

class LoginAttemptModelIntegrationTest extends TestCase
{
    private LoginAttemptModel $model;
    private string $testEmail = 'test@example.com';
    private string $testIP = '192.168.1.100';

    protected function setUp(): void
    {
        // Nettoyer la session globale pour éviter les interférences entre tests
        // Le modèle gérera session_start() lui-même
        $_SESSION = [];
        
        $this->model = new LoginAttemptModel();
    }

    protected function tearDown(): void
    {
        // Nettoyer $_SESSION après chaque test
        $_SESSION = [];
    }

    /**
     * @testdox Enregistre une tentative de connexion échouée et incrémente le compteur
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRecordFailedAttemptIncreasesCount(): void
    {
        // Arrange - Aucune tentative au départ
        $initialCount = $this->model->getFailedAttemptsCount($this->testEmail);
        
        // Act - Enregistrer une tentative échouée
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        
        // Assert - Le compteur doit avoir augmenté de 1
        $newCount = $this->model->getFailedAttemptsCount($this->testEmail);
        $this->assertEquals($initialCount + 1, $newCount);
        $this->assertEquals(1, $newCount);
    }

    /**
     * @testdox Enregistre plusieurs tentatives échouées consécutives
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRecordMultipleFailedAttempts(): void
    {
        // Act - Enregistrer 3 tentatives échouées
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        
        // Assert - Le compteur doit être à 3
        $count = $this->model->getFailedAttemptsCount($this->testEmail);
        $this->assertEquals(3, $count);
    }

    /**
     * @testdox Le compte n'est pas bloqué avant 4 tentatives
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAccountNotBlockedBefore4Attempts(): void
    {
        // Act - Enregistrer 3 tentatives (sous le seuil)
        for ($i = 0; $i < 3; $i++) {
            $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        }
        
        // Assert - Le compte ne doit PAS être bloqué
        $status = $this->model->isAccountBlocked($this->testEmail);
        $this->assertFalse($status['blocked']);
        $this->assertEquals(0, $status['remaining_time']);
        $this->assertEquals(3, $status['attempts']);
    }

    /**
     * @testdox Le compte est bloqué après 4 tentatives (1 minute)
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAccountBlockedAfter4Attempts(): void
    {
        // Act - Enregistrer 4 tentatives (atteint le seuil)
        for ($i = 0; $i < 4; $i++) {
            $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        }
        
        // Assert - Le compte doit être bloqué
        $status = $this->model->isAccountBlocked($this->testEmail);
        $this->assertTrue($status['blocked']);
        $this->assertGreaterThan(0, $status['remaining_time']);
        $this->assertEquals(4, $status['attempts']);
        $this->assertEquals(1, $status['block_duration']); // 1 minute pour 4 tentatives
    }

    /**
     * @testdox La durée de blocage augmente de façon exponentielle
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testBlockDurationIsExponential(): void
    {
        // Act & Assert - Vérifier la progression exponentielle
        
        // 4ème tentative = 1 minute
        for ($i = 0; $i < 4; $i++) {
            $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        }
        $status4 = $this->model->isAccountBlocked($this->testEmail);
        $this->assertEquals(1, $status4['block_duration']);
        
        // 5ème tentative = 2 minutes (2^1)
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        $status5 = $this->model->isAccountBlocked($this->testEmail);
        $this->assertEquals(2, $status5['block_duration']);
        
        // 6ème tentative = 4 minutes (2^2)
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        $status6 = $this->model->isAccountBlocked($this->testEmail);
        $this->assertEquals(4, $status6['block_duration']);
        
        // 7ème tentative = 8 minutes (2^3)
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        $status7 = $this->model->isAccountBlocked($this->testEmail);
        $this->assertEquals(8, $status7['block_duration']);
    }

    /**
     * @testdox La durée de blocage est plafonnée à 60 minutes maximum
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testBlockDurationIsCappedAt60Minutes(): void
    {
        // Act - Enregistrer 15 tentatives (devrait dépasser 60 minutes sans cap)
        for ($i = 0; $i < 15; $i++) {
            $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        }
        
        // Assert - La durée doit être plafonnée à 60 minutes
        $status = $this->model->isAccountBlocked($this->testEmail);
        $this->assertTrue($status['blocked']);
        $this->assertEquals(60, $status['block_duration']); // Cap à 60 minutes
    }

    /**
     * @testdox Efface les tentatives échouées après une connexion réussie
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testClearFailedAttemptsResetsCount(): void
    {
        // Arrange - Enregistrer plusieurs tentatives
        for ($i = 0; $i < 5; $i++) {
            $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        }
        $this->assertGreaterThan(0, $this->model->getFailedAttemptsCount($this->testEmail));
        
        // Act - Effacer les tentatives (connexion réussie)
        $this->model->clearFailedAttempts($this->testEmail);
        
        // Assert - Le compteur doit être remis à zéro
        $count = $this->model->getFailedAttemptsCount($this->testEmail);
        $this->assertEquals(0, $count);
        
        // Le compte ne doit plus être bloqué
        $status = $this->model->isAccountBlocked($this->testEmail);
        $this->assertFalse($status['blocked']);
    }

    /**
     * @testdox Compte les tentatives par adresse IP
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetFailedAttemptsCountByIP(): void
    {
        // Act - Enregistrer des tentatives depuis la même IP avec différents emails
        $this->model->recordFailedAttempt('user1@example.com', $this->testIP);
        $this->model->recordFailedAttempt('user2@example.com', $this->testIP);
        $this->model->recordFailedAttempt('user3@example.com', $this->testIP);
        
        // Assert - L'IP doit avoir 3 tentatives au total
        $ipCount = $this->model->getFailedAttemptsCountByIP($this->testIP);
        $this->assertEquals(3, $ipCount);
    }

    /**
     * @testdox L'IP n'est pas bloquée avant 10 tentatives
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIPNotBlockedBefore10Attempts(): void
    {
        // Act - Enregistrer 9 tentatives (sous le seuil IP)
        for ($i = 0; $i < 9; $i++) {
            $this->model->recordFailedAttempt("user{$i}@example.com", $this->testIP);
        }
        
        // Assert - L'IP ne doit PAS être bloquée
        $status = $this->model->isIPBlocked($this->testIP);
        $this->assertFalse($status['blocked']);
        $this->assertEquals(0, $status['remaining_time']);
        $this->assertEquals(9, $status['attempts']);
    }

    /**
     * @testdox L'IP est bloquée après 10 tentatives (30 minutes)
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIPBlockedAfter10Attempts(): void
    {
        // Act - Enregistrer 10 tentatives depuis la même IP
        for ($i = 0; $i < 10; $i++) {
            $this->model->recordFailedAttempt("user{$i}@example.com", $this->testIP);
        }
        
        // Assert - L'IP doit être bloquée
        $status = $this->model->isIPBlocked($this->testIP);
        $this->assertTrue($status['blocked']);
        $this->assertGreaterThan(0, $status['remaining_time']);
        $this->assertEquals(10, $status['attempts']);
    }

    /**
     * @testdox Nettoie toutes les tentatives expirées (cleanup global)
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCleanupOldAttemptsRemovesExpiredData(): void
    {
        // Arrange - Enregistrer des tentatives
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        $this->model->recordFailedAttempt('other@example.com', $this->testIP);
        
        // Vérifier qu'il y a des données
        $this->assertGreaterThan(0, $this->model->getFailedAttemptsCount($this->testEmail));
        
        // Act - Appeler le nettoyage (en conditions normales, supprime seulement les anciennes)
        $this->model->cleanupOldAttempts();
        
        // Assert - Les tentatives récentes doivent toujours être présentes
        // (car elles ne sont pas expirées)
        $count = $this->model->getFailedAttemptsCount($this->testEmail);
        $this->assertGreaterThan(0, $count);
    }

    /**
     * @testdox Deux emails différents ont des compteurs indépendants
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testDifferentEmailsHaveIndependentCounters(): void
    {
        $email1 = 'user1@example.com';
        $email2 = 'user2@example.com';
        
        // Act - Enregistrer différents nombres de tentatives
        $this->model->recordFailedAttempt($email1, $this->testIP);
        $this->model->recordFailedAttempt($email1, $this->testIP);
        
        $this->model->recordFailedAttempt($email2, $this->testIP);
        $this->model->recordFailedAttempt($email2, $this->testIP);
        $this->model->recordFailedAttempt($email2, $this->testIP);
        
        // Assert - Chaque email doit avoir son propre compteur
        $count1 = $this->model->getFailedAttemptsCount($email1);
        $count2 = $this->model->getFailedAttemptsCount($email2);
        
        $this->assertEquals(2, $count1);
        $this->assertEquals(3, $count2);
    }

    /**
     * @testdox Le temps restant de blocage est calculé correctement
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRemainingTimeIsCalculatedCorrectly(): void
    {
        // Act - Enregistrer 4 tentatives pour bloquer le compte (1 minute)
        for ($i = 0; $i < 4; $i++) {
            $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        }
        
        // Assert - Le temps restant doit être proche de 60 secondes (1 minute)
        $status = $this->model->isAccountBlocked($this->testEmail);
        $this->assertTrue($status['blocked']);
        $this->assertGreaterThan(50, $status['remaining_time']); // Au moins 50 secondes
        $this->assertLessThanOrEqual(60, $status['remaining_time']); // Max 60 secondes
    }

    /**
     * @testdox Retourne un statut de blocage valide avec tous les champs requis
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIsAccountBlockedReturnsValidStructure(): void
    {
        // Act - Enregistrer une tentative
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        
        // Assert - Vérifier la structure du retour
        $status = $this->model->isAccountBlocked($this->testEmail);
        
        $this->assertIsArray($status);
        $this->assertArrayHasKey('blocked', $status);
        $this->assertArrayHasKey('remaining_time', $status);
        $this->assertArrayHasKey('attempts', $status);
        $this->assertArrayHasKey('block_duration', $status);
        
        $this->assertIsBool($status['blocked']);
        $this->assertIsInt($status['remaining_time']);
        $this->assertIsInt($status['attempts']);
        $this->assertIsInt($status['block_duration']);
    }

    /**
     * @testdox Retourne un statut IP valide avec tous les champs requis
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIsIPBlockedReturnsValidStructure(): void
    {
        // Act - Enregistrer une tentative
        $this->model->recordFailedAttempt($this->testEmail, $this->testIP);
        
        // Assert - Vérifier la structure du retour
        $status = $this->model->isIPBlocked($this->testIP);
        
        $this->assertIsArray($status);
        $this->assertArrayHasKey('blocked', $status);
        $this->assertArrayHasKey('remaining_time', $status);
        $this->assertArrayHasKey('attempts', $status);
        
        $this->assertIsBool($status['blocked']);
        $this->assertIsInt($status['remaining_time']);
        $this->assertIsInt($status['attempts']);
    }
}
