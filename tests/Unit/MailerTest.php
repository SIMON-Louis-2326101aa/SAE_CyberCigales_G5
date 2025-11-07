<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la validation des emails
 * 
 * @group unit
 * @group mailer
 */
class MailerTest extends TestCase
{
    /**
     * Teste la validation du format d'email
     * 
     * Vérifie que la fonction filter_var() avec FILTER_VALIDATE_EMAIL
     * valide correctement différents formats d'emails (valides et invalides).
     * 
     * @param string $email L'adresse email à tester
     * @param bool $expected La valeur attendue (true si valide, false sinon)
     * @dataProvider emailProvider
     */
    public function testEmailValidation(string $email, bool $expected): void
    {
        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        $this->assertEquals($expected, $isValid);
    }
    
    /**
     * Fournisseur de données pour les tests de validation d'email
     * 
     * Retourne un tableau de cas de test couvrant :
     * - Emails valides standards (user@example.com)
     * - Emails valides avec sous-domaines
     * - Emails valides avec caractères spéciaux (+ et -)
     * - Emails invalides (sans @, sans domaine, avec espaces)
     * 
     * @return array Tableau associatif de cas de test [nom => [email, valeurAttendue]]
     */
    public function emailProvider(): array
    {
        return [
            'valid email' => ['user@example.com', true],
            'valid with subdomain' => ['user@mail.example.com', true],
            'invalid no @' => ['userexample.com', false],
            'invalid no domain' => ['user@', false],
            'invalid spaces' => ['user @example.com', false],
            'valid with plus' => ['user+test@example.com', true],
            'valid with dash' => ['user-name@example.com', true],
        ];
    }
    
    /**
     * Teste le nettoyage des emails (trim et lowercase)
     * 
     * Vérifie que les emails sont correctement nettoyés :
     * - Suppression des espaces en début et fin (trim)
     * - Conversion en minuscules (strtolower)
     * 
     * Cela permet d'assurer une normalisation des emails avant stockage
     * et de traiter "User@Example.COM" et "user@example.com" comme identiques.
     */
    public function testEmailSanitization(): void
    {
        $dirtyEmail = '  User@Example.COM  ';
        $cleanEmail = strtolower(trim($dirtyEmail));
        
        $this->assertEquals('user@example.com', $cleanEmail);
    }
}

