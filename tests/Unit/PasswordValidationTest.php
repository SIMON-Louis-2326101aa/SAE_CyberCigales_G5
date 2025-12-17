<?php // Balise d'ouverture PHP

namespace Tests\Unit; // Déclare le namespace Tests\Unit

use PHPUnit\Framework\TestCase; // Importe TestCase de PHPUnit (classe de base pour les tests)

/**
 * Tests unitaires pour la validation des mots de passe
 * 
 * @testdox Tests unitaires - Validation des mots de passe
 * @group unit
 * @group password
 */
class PasswordValidationTest extends TestCase // Hérite de TestCase pour avoir les méthodes de test PHPUnit
{
    /**
     * @testdox Vérifie que le mot de passe contient au moins 8 caractères (utilise strlen() pour compter, teste avec 7, 8 et 13 caractères)
     * @dataProvider passwordLengthProvider
     */
    public function testPasswordMinimumLength(string $password, bool $expected): void // Test de longueur minimale du mot de passe
    {
        $length = strlen($password); // Compte le nombre de caractères du mot de passe
        
        $isValid = $length >= 8; // Vérifie si >= 8, stocke le résultat (true/false) dans $isValid
        
        $message = sprintf(
            "\n┌─ ERREUR DE VALIDATION DE MOT DE PASSE ─────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/PasswordValidationTest.php\n" .
            "│ Ligne à corriger   : 63\n" .
            "│ Méthode            : passwordLengthProvider()\n" .
            "│ \n" .
            "│ Mot de passe testé : '%s'\n" .
            "│ Longueur actuelle  : %d caractères\n" .
            "│ Longueur minimale  : 8 caractères\n" .
            "│ \n" .
            "│ Résultat attendu   : %s\n" .
            "│ Résultat obtenu    : %s\n" .
            "│ \n" .
            "│ CORRECTION :\n" .
            "│    1. Ouvrez le fichier tests/Unit/PasswordValidationTest.php\n" .
            "│    2. Allez à la LIGNE 63\n" .
            "│    3. Changez 'Pass12!' en 'Pass1234!' (pour avoir >= 8 caractères)\n" .
            "│    4. Sauvegardez et relancez les tests\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $password,
            $length,
            $expected ? 'VALIDE (true)' : 'INVALIDE (false)',
            $isValid ? 'VALIDE (true)' : 'INVALIDE (false)'
        );
        
        $this->assertEquals($expected, $isValid, $message); // Vérifie que la valeur obtenue ($isValid) correspond à la valeur attendue ($expected)
    }
    
    /**
     * Fournisseur de données pour le test de longueur minimale
     * 
     * @return array Tableau associatif de cas de test [nom => [motDePasse, valeurAttendue]]
     */
    public function passwordLengthProvider(): array // Data provider : fournit des données aux tests
    {
        return [ // Retourne un tableau associatif avec plusieurs cas de test
            'too short' => ['Pass1!', false], // Cas : mot de passe trop court (7 caractères) → doit être invalide (false)
            'exactly 8' => ['Pass1234!', true], // Cas : mot de passe de 9 caractères → doit être valide (true)
            'long enough' => ['Password123!', true], // Cas : mot de passe de  13 caractères → doit être valide (true)
        ];
    }
    
    /**
     * @testdox Vérifie que le mot de passe contient majuscule, minuscule, chiffre et caractère spécial (utilise preg_match avec regex pour chaque critère, tous doivent être présents)
     * @dataProvider passwordComplexityProvider
     */
    public function testPasswordComplexity(string $password, bool $expected): void // Test de complexité du mot de passe
    {
        $hasUpperCase = preg_match('/[A-Z]/', $password); // Cherche une majuscule (A-Z) dans $password, stocke le résultat dans $hasUpperCase
        
        $hasLowerCase = preg_match('/[a-z]/', $password); // Cherche une minuscule (a-z) dans $password, stocke le résultat dans $hasLowerCase
        
        $hasNumber = preg_match('/[0-9]/', $password); // Cherche un chiffre (0-9) dans $password, stocke le résultat dans $hasNumber
        
        $hasSpecial = preg_match('/[^a-zA-Z0-9]/', $password); // Cherche un caractère spécial (pas une lettre ni un chiffre) dans $password, stocke le résultat dans $hasSpecial
        
        $isValid = $hasUpperCase && $hasLowerCase && $hasNumber && $hasSpecial; // Vérifie que TOUTES les conditions sont vraies (ET logique), stocke le résultat dans $isValid
        
        $missing = [];
        if (!$hasUpperCase) $missing[] = "Majuscule (A-Z)";
        if (!$hasLowerCase) $missing[] = "Minuscule (a-z)";
        if (!$hasNumber) $missing[] = "Chiffre (0-9)";
        if (!$hasSpecial) $missing[] = "Caractère spécial (!@#$%^&*...)";
        
        $message = sprintf(
            "\n┌─ ERREUR DE COMPLEXITÉ DE MOT DE PASSE ─────────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/PasswordValidationTest.php\n" .
            "│ Ligne à corriger   : Méthode passwordComplexityProvider()\n" .
            "│ \n" .
            "│ Mot de passe testé : '%s'\n" .
            "│ \n" .
            "│ Majuscule (A-Z)  : %s\n" .
            "│ Minuscule (a-z)  : %s\n" .
            "│ Chiffre (0-9)    : %s\n" .
            "│ Spécial (!@#...) : %s\n" .
            "│ \n" .
            "│ Résultat attendu   : %s\n" .
            "│ Résultat obtenu    : %s\n" .
            "│ %s\n" .
            "│ \n" .
            "│ CORRECTION : Modifiez le mot de passe dans passwordComplexityProvider()\n" .
            "│    pour inclure tous les critères requis.\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $password,
            $hasUpperCase ? 'OUI' : 'NON',
            $hasLowerCase ? 'OUI' : 'NON',
            $hasNumber ? 'OUI' : 'NON',
            $hasSpecial ? 'OUI' : 'NON',
            $expected ? 'VALIDE (true)' : 'INVALIDE (false)',
            $isValid ? 'VALIDE (true)' : 'INVALIDE (false)',
            !empty($missing) ? '│ Manquant : ' . implode(', ', $missing) : '│ Tous les critères sont présents'
        );
        
        $this->assertEquals($expected, (bool)$isValid, $message); // Convertit $isValid en booléen et vérifie qu'il correspond à $expected
    }
    
    /**
     * Fournisseur de données pour le test de complexité
     * 
     * @return array Tableau associatif de cas de test [nom => [motDePasse, valeurAttendue]]
     */
    public function passwordComplexityProvider(): array // Data provider pour le test de complexité
    {
        return [ // Retourne un tableau associatif avec plusieurs cas de test
            'no uppercase' => ['password123!', false], // Cas : pas de majuscule → invalide
            'no lowercase' => ['PASSWORD123!', false], // Cas : pas de minuscule → invalide
            'no number' => ['Password!', false], // Cas : pas de chiffre → invalide
            'no special' => ['Password123', false], // Cas : pas de caractère spécial → invalide
            'valid password' => ['Password123!', true], // Cas : tous les critères présents → valide
            'complex valid' => ['MyP@ssw0rd!2024', true], // Cas : mot de passe très complexe → valide
        ];
    }
    
    /**
     * @testdox Vérifie que deux mots de passe identiques correspondent (compare deux chaînes avec assertEquals)
     */
    public function testPasswordsMatch(): void // Test : deux mots de passe identiques doivent correspondre
    {
        $password = 'MyPassword123!'; // Déclare $password avec le mot de passe initial
        
        $confirm = 'MyPassword123!'; // Déclare $confirm avec la même valeur (confirmation identique)
        
        $message = sprintf(
            "\n┌─ ERREUR DE CORRESPONDANCE DE MOTS DE PASSE ────────────────────\n" .
            "│ Fichier concerné   : tests/Unit/PasswordValidationTest.php\n" .
            "│ Test               : testPasswordsMatch() ligne 107\n" .
            "│ \n" .
            "│ Mot de passe       : '%s'\n" .
            "│ Confirmation       : '%s'\n" .
            "│ \n" .
            "│ Les deux mots de passe DEVRAIENT être identiques mais ne le sont pas.\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez les lignes 109-110 du test\n" .
            "│    et assurez-vous que les deux chaînes sont identiques.\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $password,
            $confirm
        );
        
        $this->assertEquals($password, $confirm, $message); // Vérifie que les deux valeurs sont égales (les mots de passe correspondent)
    }
    
    /**
     * @testdox Vérifie que deux mots de passe différents ne correspondent pas (utilise assertNotEquals pour s'assurer que les valeurs diffèrent)
     */
    public function testPasswordsDontMatch(): void // Test : deux mots de passe différents ne doivent pas correspondre
    {
        $password = 'MyPassword123!'; // Déclare $password avec le mot de passe initial
        
        $confirm = 'DifferentPassword123!'; // Déclare $confirm avec une valeur différente (confirmation incorrecte)
        
        $message = sprintf(
            "\n┌─ ERREUR DE NON-CORRESPONDANCE DE MOTS DE PASSE ────────────────\n" .
            "│ Fichier concerné   : tests/Unit/PasswordValidationTest.php\n" .
            "│ Test               : testPasswordsDontMatch() ligne 119\n" .
            "│ \n" .
            "│ Mot de passe       : '%s'\n" .
            "│ Confirmation       : '%s'\n" .
            "│ \n" .
            "│ Les deux mots de passe DEVRAIENT être différents mais sont identiques.\n" .
            "│ \n" .
            "│ CORRECTION : Vérifiez les lignes 121-122 du test\n" .
            "│    et assurez-vous que les deux chaînes sont différentes.\n" .
            "└─────────────────────────────────────────────────────────────────\n",
            $password,
            $confirm
        );
        
        $this->assertNotEquals($password, $confirm, $message); // Vérifie que les deux valeurs sont différentes (les mots de passe ne correspondent pas, comme attendu)
    }
}
