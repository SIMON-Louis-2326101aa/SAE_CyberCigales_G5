<?php // Balise d'ouverture PHP

namespace Tests\Unit; // Déclare le namespace Tests\Unit

use PHPUnit\Framework\TestCase; // Importe TestCase de PHPUnit (classe de base pour les tests)

/**
 * Tests unitaires pour la validation des emails
 * 
 * @testdox Tests unitaires - Validation des emails
 * @group unit
 * @group mailer
 */
class MailerTest extends TestCase // Hérite de TestCase pour avoir les méthodes de test PHPUnit
{
    /**
     * @testdox Valide le format d'email avec différents cas (utilise filter_var FILTER_VALIDATE_EMAIL, teste valides: user@example.com, invalides: sans @, avec espaces)
     * @dataProvider emailProvider
     */
    public function testEmailValidation(string $email, bool $expected): void // Test de validation du format d'email
    {
        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false; // Valide l'email avec filter_var, retourne true si valide (pas false), stocke dans $isValid
        
        $message = sprintf(
            "\n┌─ ERREUR DE VALIDATION D'EMAIL ──────────────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/MailerTest.php\n" .
            "│ Ligne à corriger   : Méthode emailProvider()\n" .
            "│ \n" .
            "│ Email testé        : '%s'\n" .
            "│ \n" .
            "│ Résultat attendu   : %s\n" .
            "│ Résultat obtenu    : %s\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez emailProvider() dans MailerTest.php\n" .
            "│    Un email %s mais le test attend qu'il soit %s.\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $email,
            $expected ? 'VALIDE (true)' : 'INVALIDE (false)',
            $isValid ? 'VALIDE (true)' : 'INVALIDE (false)',
            $isValid ? 'est valide' : 'est invalide',
            $expected ? 'valide' : 'invalide'
        );
        
        $this->assertEquals($expected, $isValid, $message); // Vérifie que la valeur obtenue ($isValid) correspond à la valeur attendue ($expected)
    }
    
    /**
     * Fournisseur de données pour les tests de validation d'email
     * 
     * @return array Tableau associatif de cas de test [nom => [email, valeurAttendue]]
     */
    public function emailProvider(): array // Data provider : fournit des emails à tester
    {
        return [ // Retourne un tableau associatif avec plusieurs cas de test
            'valid email' => ['user@example.com', true], // Cas : email valide standard → doit être valide
            'valid with subdomain' => ['user@mail.example.com', true], // Cas : email avec sous-domaine → doit être valide
            'invalid no @' => ['userexample.com', false], // Cas : email sans @ → doit être invalide
            'invalid no domain' => ['user@', false], // Cas : email sans domaine → doit être invalide
            'invalid spaces' => ['user @example.com', false], // Cas : email avec espaces → doit être invalide
            'valid with plus' => ['user+test@example.com', true], // Cas : email avec + → doit être valide
            'valid with dash' => ['user-name@example.com', true], // Cas : email avec tiret → doit être valide
        ];
    }
    
    /**
     * @testdox Nettoie les emails (utilise trim() pour supprimer les espaces puis strtolower() pour convertir en minuscules, "  User@Example.COM  " devient "user@example.com")
     */
    public function testEmailSanitization(): void // Test de nettoyage (sanitization) des emails
    {
        $dirtyEmail = '  User@Example.COM  '; // Déclare $dirtyEmail avec un email "sale" (espaces + majuscules)
        
        $cleanEmail = strtolower(trim($dirtyEmail)); // Supprime les espaces (trim) puis convertit en minuscules (strtolower), stocke dans $cleanEmail
        
        $message = sprintf(
            "\n┌─ ERREUR DE NETTOYAGE D'EMAIL ───────────────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/MailerTest.php\n" .
            "│ Test               : testEmailSanitization() ligne 48\n" .
            "│ \n" .
            "│ Email original     : '%s'\n" .
            "│ Email nettoyé      : '%s'\n" .
            "│ Email attendu      : 'user@example.com'\n" .
            "│ \n" .
            "│ Le nettoyage devrait :\n" .
            "│ 1. Supprimer les espaces (trim)\n" .
            "│ 2. Convertir en minuscules (strtolower)\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez la ligne 52 du test\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $dirtyEmail,
            $cleanEmail
        );
        
        $this->assertEquals('user@example.com', $cleanEmail, $message); // Vérifie que l'email nettoyé correspond à la valeur attendue
    }
}
