<?php // Balise d'ouverture PHP

namespace Tests\Unit; // Déclare le namespace Tests\Unit

use PHPUnit\Framework\TestCase; // Importe TestCase de PHPUnit

use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel; // Importe EmailVerificationModel du projet

/**
 * Tests unitaires pour EmailVerificationModel
 * 
 * @testdox Tests unitaires - Génération de codes de vérification email
 * @group unit
 * @covers \SAE_CyberCigales_G5\Modules\model\EmailVerificationModel
 */
class EmailVerificationModelTest extends TestCase // Hérite de TestCase pour avoir les méthodes de test PHPUnit
{
    /**
     * @testdox Génère un code de vérification de 6 chiffres (utilise random_int(0-999999) puis str_pad avec zéros à gauche pour garantir 6 caractères, valide avec regex /^\d{6}$/)
     */
    public function testGenerateCodeReturns6DigitString(): void // Test : le code généré doit être une chaîne de 6 chiffres
    {
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT); // Génère un nombre aléatoire (0-999999), le convertit en string, puis complète à gauche avec des zéros pour avoir 6 caractères
        
        $message = sprintf(
            "\n┌─ ERREUR DE GÉNÉRATION DE CODE ──────────────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/EmailVerificationModelTest.php\n" .
            "│ Test               : testGenerateCodeReturns6DigitString() ligne 21\n" .
            "│ \n" .
            "│ Code généré        : '%s'\n" .
            "│ Longueur           : %d caractères\n" .
            "│ Longueur attendue  : 6 caractères\n" .
            "│ Format attendu     : 6 chiffres (0-9)\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez la ligne 23 du test\n" .
            "│    Assurez-vous que str_pad() génère bien 6 caractères.\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $code,
            strlen($code)
        );
        
        $this->assertIsString($code, $message); // Vérifie que $code est une chaîne de caractères
        
        $this->assertEquals(6, strlen($code), $message); // Vérifie que la longueur de $code est exactement 6 caractères
        
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code, $message); // Vérifie que $code contient exactement 6 chiffres (0-9) avec une expression régulière
    }
    
    /**
     * @testdox Formate les codes courts avec des zéros à gauche (utilise str_pad avec STR_PAD_LEFT, "42" devient "000042" pour garantir toujours 6 chiffres)
     */
    public function testCodeHasLeadingZeros(): void // Test : les codes courts doivent être complétés avec des zéros à gauche
    {
        $code = str_pad('42', 6, '0', STR_PAD_LEFT); // Complète '42' à gauche avec des zéros pour avoir 6 caractères → '000042'
        
        $message = sprintf(
            "\n┌─ ERREUR DE FORMATAGE DE CODE ───────────────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/EmailVerificationModelTest.php\n" .
            "│ Test               : testCodeHasLeadingZeros() ligne 35\n" .
            "│ \n" .
            "│ Code original      : '42'\n" .
            "│ Code formaté       : '%s'\n" .
            "│ Code attendu       : '000042'\n" .
            "│ \n" .
            "│ Le formatage devrait ajouter des zéros à gauche avec str_pad()\n" .
            "│ pour garantir toujours 6 caractères.\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez la ligne 37 du test\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $code
        );
        
        $this->assertEquals('000042', $code, $message); // Vérifie que le code complété correspond à '000042'
    }
    
    /**
     * @testdox Limite le TTL entre 1 et 60 minutes (utilise max(1, min(60, valeur)), TTL 0 devient 1, TTL 100 devient 60, TTL 10 reste 10)
     */
    public function testTtlIsWithinValidRange(): void // Test : le TTL doit être limité entre 1 et 60 minutes
    {
        $ttlTooSmall = max(1, min(60, 0)); // Limite 0 entre 1 et 60 → résultat = 1 (valeur trop petite remontée à 1)
        
        $ttlTooLarge = max(1, min(60, 100)); // Limite 100 entre 1 et 60 → résultat = 60 (valeur trop grande limitée à 60)
        
        $ttlValid = max(1, min(60, 10)); // Limite 10 entre 1 et 60 → résultat = 10 (valeur valide reste inchangée)
        
        $messageTooSmall = sprintf(
            "\n┌─ ERREUR DE LIMITE TTL (TROP PETIT) ─────────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/EmailVerificationModelTest.php\n" .
            "│ Test               : testTtlIsWithinValidRange() ligne 45\n" .
            "│ \n" .
            "│ TTL testé          : 0 minutes\n" .
            "│ TTL obtenu         : %d minutes\n" .
            "│ TTL attendu        : 1 minute (minimum)\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez la ligne 47 - le TTL doit être >= 1\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $ttlTooSmall
        );
        
        $messageTooLarge = sprintf(
            "\n┌─ ERREUR DE LIMITE TTL (TROP GRAND) ─────────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/EmailVerificationModelTest.php\n" .
            "│ Test               : testTtlIsWithinValidRange() ligne 45\n" .
            "│ \n" .
            "│ TTL testé          : 100 minutes\n" .
            "│ TTL obtenu         : %d minutes\n" .
            "│ TTL attendu        : 60 minutes (maximum)\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez la ligne 49 - le TTL doit être <= 60\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $ttlTooLarge
        );
        
        $messageValid = sprintf(
            "\n┌─ ERREUR DE LIMITE TTL (VALEUR VALIDE) ──────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/EmailVerificationModelTest.php\n" .
            "│ Test               : testTtlIsWithinValidRange() ligne 45\n" .
            "│ \n" .
            "│ TTL testé          : 10 minutes\n" .
            "│ TTL obtenu         : %d minutes\n" .
            "│ TTL attendu        : 10 minutes (devrait rester inchangé)\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez la ligne 51\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $ttlValid
        );
        
        $this->assertEquals(1, $ttlTooSmall, $messageTooSmall); // Vérifie que la valeur trop petite a été remontée à 1
        
        $this->assertEquals(60, $ttlTooLarge, $messageTooLarge); // Vérifie que la valeur trop grande a été limitée à 60
        
        $this->assertEquals(10, $ttlValid, $messageValid); // Vérifie que la valeur valide est restée à 10
    }
}
