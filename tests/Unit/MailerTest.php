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
     * @dataProvider emailProvider
     */
    public function testEmailValidation(string $email, bool $expected): void
    {
        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        $this->assertEquals($expected, $isValid);
    }
    
    /**
     * Fournisseur de données pour les emails
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
     */
    public function testEmailSanitization(): void
    {
        $dirtyEmail = '  User@Example.COM  ';
        $cleanEmail = strtolower(trim($dirtyEmail));
        
        $this->assertEquals('user@example.com', $cleanEmail);
    }
}

