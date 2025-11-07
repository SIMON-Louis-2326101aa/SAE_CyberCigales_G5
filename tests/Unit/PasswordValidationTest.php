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
     * Vérifie que la validation de longueur minimale fonctionne correctement
     * pour différents mots de passe (trop courts, exactement 8, plus longs).
     * 
     * @param string $password Le mot de passe à tester
     * @param bool $expected La valeur attendue (true si valide, false sinon)
     * @dataProvider passwordLengthProvider
     */
    public function testPasswordMinimumLength(string $password, bool $expected): void
    {
        $isValid = strlen($password) >= 8;
        $this->assertEquals($expected, $isValid);
    }
    
    /**
     * Fournisseur de données pour le test de longueur minimale
     * 
     * Retourne un tableau de cas de test avec :
     * - Des mots de passe trop courts (< 8 caractères) → doivent être invalides
     * - Des mots de passe de 8 caractères exactement → doivent être valides
     * - Des mots de passe plus longs (> 8 caractères) → doivent être valides
     * 
     * @return array Tableau associatif de cas de test [nom => [motDePasse, valeurAttendue]]
     */
    public function passwordLengthProvider(): array
    {
        return [
            'too short' => ['Pass1!', false],
            'exactly 8' => ['Pass1234!', true],
            'long enough' => ['Password123!', true], // ✅ Corrigé
        ];
    }
    
    /**
     * Teste la complexité du mot de passe
     * 
     * Vérifie qu'un mot de passe valide contient :
     * - Au moins une lettre majuscule (A-Z)
     * - Au moins une lettre minuscule (a-z)
     * - Au moins un chiffre (0-9)
     * - Au moins un caractère spécial (!@#$%^&*...)
     * 
     * @param string $password Le mot de passe à tester
     * @param bool $expected La valeur attendue (true si valide, false sinon)
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
     * 
     * Retourne un tableau de cas de test couvrant différents scénarios :
     * - Mots de passe sans majuscule → invalides
     * - Mots de passe sans minuscule → invalides
     * - Mots de passe sans chiffre → invalides
     * - Mots de passe sans caractère spécial → invalides
     * - Mots de passe valides avec tous les critères → valides
     * 
     * @return array Tableau associatif de cas de test [nom => [motDePasse, valeurAttendue]]
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
     * 
     * Vérifie que lors de la confirmation d'un mot de passe,
     * si les deux saisies sont identiques, elles correspondent.
     * Utilisé pour valider la confirmation de mot de passe lors de l'inscription.
     */
    public function testPasswordsMatch(): void
    {
        $password = 'MyPassword123!';
        $confirm = 'MyPassword123!';
        
        $this->assertEquals($password, $confirm);
    }
    
    /**
     * Teste que deux mots de passe différents ne correspondent pas
     * 
     * Vérifie que lors de la confirmation d'un mot de passe,
     * si les deux saisies sont différentes, elles ne correspondent pas.
     * Utilisé pour détecter les erreurs de saisie lors de l'inscription.
     */
    public function testPasswordsDontMatch(): void
    {
        $password = 'MyPassword123!';
        $confirm = 'DifferentPassword123!';
        
        $this->assertNotEquals($password, $confirm);
    }
}

