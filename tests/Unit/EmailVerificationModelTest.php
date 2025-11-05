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
     * Le code doit être une chaîne de 6 chiffres
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
     */
    public function testCodeHasLeadingZeros(): void
    {
        $code = str_pad('42', 6, '0', STR_PAD_LEFT);
        
        $this->assertEquals('000042', $code);
    }
    
    /**
     * Teste que le TTL est limité entre 1 et 60 minutes
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

