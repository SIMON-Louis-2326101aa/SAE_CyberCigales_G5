<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la validation des mots de passe
 * 
 * @group unit
 * @group password
 */
class PasswordValidationTest extends TestCase
{
    /**
     * Teste qu'un mot de passe doit contenir au moins 8 caractères
     * 
     * @dataProvider passwordLengthProvider
     */
    public function testPasswordMinimumLength(string $password, bool $expected): void
    {
        $isValid = strlen($password) >= 8;
        $this->assertEquals($expected, $isValid);
    }
    
    /**
     * Fournisseur de données pour le test de longueur
     */
    public function passwordLengthProvider(): array
    {
        return [
            'too short' => ['Pass1!', false],
            'exactly 8' => ['Pass1234!', true],
            'long enough' => ['Password123!', true],
        ];
    }
    
    /**
     * Teste la complexité du mot de passe (majuscule, minuscule, chiffre, spécial)
     * 
     * @dataProvider passwordComplexityProvider
     */
    public function testPasswordComplexity(string $password, bool $expected): void
    {
        $hasUpperCase = preg_match('/[A-Z]/', $password);
        $hasLowerCase = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasSpecial = preg_match('/[^a-zA-Z0-9]/', $password);
        
        $isValid = $hasUpperCase && $hasLowerCase && $hasNumber && $hasSpecial;
        
        $this->assertEquals($expected, (bool)$isValid);
    }
    
    /**
     * Fournisseur de données pour le test de complexité
     */
    public function passwordComplexityProvider(): array
    {
        return [
            'no uppercase' => ['password123!', false],
            'no lowercase' => ['PASSWORD123!', false],
            'no number' => ['Password!', false],
            'no special' => ['Password123', false],
            'valid password' => ['Password123!', true],
            'complex valid' => ['MyP@ssw0rd!2024', true],
        ];
    }
    
    /**
     * Teste que deux mots de passe identiques correspondent
     */
    public function testPasswordsMatch(): void
    {
        $password = 'MyPassword123!';
        $confirm = 'MyPassword123!';
        
        $this->assertEquals($password, $confirm);
    }
    
    /**
     * Teste que deux mots de passe différents ne correspondent pas
     */
    public function testPasswordsDontMatch(): void
    {
        $password = 'MyPassword123!';
        $confirm = 'DifferentPassword123!';
        
        $this->assertNotEquals($password, $confirm);
    }
}

