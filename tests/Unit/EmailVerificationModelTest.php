<?php

// Balise d'ouverture PHP

namespace Tests\Unit;

// Déclare le namespace Tests\Unit

use PHPUnit\Framework\TestCase;
// Importe TestCase de PHPUnit
use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel;

// Importe EmailVerificationModel du projet

/**
 * Tests unitaires pour EmailVerificationModel
 *
 * @testdox Tests unitaires - Génération de codes de vérification email
 * @group unit
 * @covers \SAE_CyberCigales_G5\Modules\model\EmailVerificationModel
 */
class EmailVerificationModelTest extends TestCase
{
    /**
     * @testdox Génère un code de vérification de 6 chiffres
     * (utilise random_int(0-999999) puis str_pad avec zéros à gauche
     * pour garantir 6 caractères, valide avec regex /^\d{6}$/)
     */
    public function testGenerateCodeReturns6DigitString(): void
    {
        // Utilise la méthode generateCode() de EmailVerificationModel
        $code = EmailVerificationModel::generateCode();

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

        // Vérifie que $code est une chaîne de caractères
        $this->assertIsString($code, $message);

        // Vérifie que la longueur de $code est exactement 6 caractères
        $this->assertEquals(6, strlen($code), $message);

        // Vérifie que $code contient exactement 6 chiffres (0-9)
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code, $message);
    }

    /**
     * @testdox Formate les codes courts avec des zéros à gauche
     * (utilise str_pad avec STR_PAD_LEFT, "42" devient "000042")
     */
    public function testCodeHasLeadingZeros(): void
    {
        // Génère plusieurs codes pour augmenter les chances
        // d'avoir un code court (< 100000)
        $allCodesHave6Digits = true;

        for ($i = 0; $i < 50; $i++) {
            // Utilise la méthode generateCode() de EmailVerificationModel
            $code = EmailVerificationModel::generateCode();

            if (strlen($code) !== 6) {
                $allCodesHave6Digits = false;
                break;
            }
        }

        $message = sprintf(
            "\n┌─ ERREUR DE FORMATAGE DE CODE ───────────────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/EmailVerificationModelTest.php\n" .
            "│ Test               : testCodeHasLeadingZeros() ligne 52\n" .
            "│ \n" .
            "│ Tous les codes générés doivent avoir exactement 6 caractères\n" .
            "│ (avec zéros à gauche si le nombre est < 100000)\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez EmailVerificationModel::generateCode()\n" .
            "└─────────────────────────────────────────────────────────────────\n"
        );

        // Vérifie que tous les codes ont bien 6 caractères
        $this->assertTrue($allCodesHave6Digits, $message);
    }

    /**
     * @testdox Limite le TTL entre 1 et 60 minutes
     * (utilise max(1, min(60, valeur)))
     */
    public function testTtlIsWithinValidRange(): void
    {
        // TTL trop petit → doit être remonté à 1
        $ttlTooSmall = EmailVerificationModel::validateTTL(0);

        // TTL trop grand → doit être limité à 60
        $ttlTooLarge = EmailVerificationModel::validateTTL(100);

        // TTL valide → doit rester inchangé
        $ttlValid = EmailVerificationModel::validateTTL(10);

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

        // Vérifie que la valeur trop petite a été remontée à 1
        $this->assertEquals(1, $ttlTooSmall, $messageTooSmall);

        // Vérifie que la valeur trop grande a été limitée à 60
        $this->assertEquals(60, $ttlTooLarge, $messageTooLarge);

        // Vérifie que la valeur valide est restée à 10
        $this->assertEquals(10, $ttlValid, $messageValid);
    }
}
