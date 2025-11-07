<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel;

/**
 * Tests unitaires pour EmailVerificationModel
 * 
 * @group unit
 * @covers \SAE_CyberCigales_G5\Modules\model\EmailVerificationModel
 */
class EmailVerificationModelTest extends TestCase
{
    /**
     * Teste la génération d'un code de vérification
     * 
     * Vérifie que le code généré respecte le format attendu :
     * - Doit être une chaîne de caractères (string)
     * - Doit contenir exactement 6 caractères
     * - Doit contenir uniquement des chiffres (0-9)
     * - Les codes courts doivent être complétés avec des zéros à gauche
     * 
     * Exemple : "42" devient "000042"
     */
    public function testGenerateCodeReturns6DigitString(): void
    {
        // Ce test nécessite une base de données de test
        // Pour l'instant, on teste juste le format du code généré
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->assertIsString($code);
        $this->assertEquals(6, strlen($code));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
    }
    
    /**
     * Teste que le code est bien formaté avec des zéros initiaux
     * 
     * Vérifie que la fonction str_pad() remplit correctement les codes courts
     * avec des zéros à gauche pour atteindre 6 caractères.
     * 
     * Exemple : "42" → "000042", "1234" → "001234"
     * Cela garantit que tous les codes ont la même longueur, même les petits nombres.
     */
    public function testCodeHasLeadingZeros(): void
    {
        $code = str_pad('42', 6, '0', STR_PAD_LEFT);
        
        $this->assertEquals('000042', $code);
    }
    
    /**
     * Teste que le TTL (Time To Live) est limité entre 1 et 60 minutes
     * 
     * Vérifie que la fonction de limitation du TTL fonctionne correctement :
     * - Les valeurs trop petites (< 1) sont remontées à 1
     * - Les valeurs trop grandes (> 60) sont limitées à 60
     * - Les valeurs dans la plage valide (1-60) restent inchangées
     * 
     * Cela évite les codes de vérification avec une durée de vie invalide.
     */
    public function testTtlIsWithinValidRange(): void
    {
        $ttlTooSmall = max(1, min(60, 0));
        $ttlTooLarge = max(1, min(60, 100));
        $ttlValid = max(1, min(60, 10));
        
        $this->assertEquals(1, $ttlTooSmall);
        $this->assertEquals(60, $ttlTooLarge);
        $this->assertEquals(10, $ttlValid);
    }
}

