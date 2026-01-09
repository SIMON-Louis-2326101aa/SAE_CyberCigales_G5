<?php
/**
 * 🎯 FICHIER DE DÉMONSTRATION - Test Manuel vs Test Automatique
 * 
 * Ce fichier sert à montrer à vos camarades comment un test manuel fonctionne,
 * avant de leur montrer que le test automatique fait exactement la même chose.
 * 
 * USAGE : php demo_test_manuel.php
 */

echo "═══════════════════════════════════════════════════════════════\n";
echo "   📝 DÉMONSTRATION : Test Manuel de Validation de Mot de Passe\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📋 RÈGLE : Un mot de passe doit contenir au moins 8 caractères\n\n";

// ==================== TEST 1 : Mot de passe trop court ====================
echo "🔍 TEST 1 : Mot de passe trop court\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$password = "Pass1!";
$longueur = strlen($password);
$isValid = $longueur >= 8;

echo "Mot de passe testé : " . $password . "\n";
echo "Longueur            : " . $longueur . " caractères\n";
echo "Valide (>= 8) ?     : " . ($isValid ? "✅ OUI" : "❌ NON") . "\n";
echo "Résultat attendu    : ❌ NON (invalide car trop court)\n";
echo "Test                : " . ($isValid === false ? "✅ RÉUSSI" : "❌ ÉCHOUÉ") . "\n\n";

// ==================== TEST 2 : Mot de passe exactement 8 caractères ====================
echo "🔍 TEST 2 : Mot de passe exactement 8 caractères\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$password = "Pass1234";
$longueur = strlen($password);
$isValid = $longueur >= 8;

echo "Mot de passe testé : " . $password . "\n";
echo "Longueur            : " . $longueur . " caractères\n";
echo "Valide (>= 8) ?     : " . ($isValid ? "✅ OUI" : "❌ NON") . "\n";
echo "Résultat attendu    : ✅ OUI (valide car >= 8)\n";
echo "Test                : " . ($isValid === true ? "✅ RÉUSSI" : "❌ ÉCHOUÉ") . "\n\n";

// ==================== TEST 3 : Mot de passe assez long ====================
echo "🔍 TEST 3 : Mot de passe long\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$password = "Password123!";
$longueur = strlen($password);
$isValid = $longueur >= 8;

echo "Mot de passe testé : " . $password . "\n";
echo "Longueur            : " . $longueur . " caractères\n";
echo "Valide (>= 8) ?     : " . ($isValid ? "✅ OUI" : "❌ NON") . "\n";
echo "Résultat attendu    : ✅ OUI (valide car >= 8)\n";
echo "Test                : " . ($isValid === true ? "✅ RÉUSSI" : "❌ ÉCHOUÉ") . "\n\n";

// ==================== RÉSUMÉ ====================
echo "═══════════════════════════════════════════════════════════════\n";
echo "   📊 RÉSUMÉ DES TESTS MANUELS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "✅ 3 tests effectués manuellement\n";
echo "⏱️  Temps nécessaire : ~2-3 minutes (écriture + exécution)\n";
echo "🔄 Répétabilité      : Il faut tout retaper à chaque fois\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "   🚀 MAINTENANT, REGARDEZ LE TEST AUTOMATIQUE !\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Lancez cette commande :\n";
echo "👉 php vendor/phpunit/phpunit/phpunit tests/Unit/PasswordValidationTest.php\n\n";

echo "Vous verrez :\n";
echo "  • 10 tests au lieu de 3\n";
echo "  • Exécution en 0.05 seconde au lieu de 2-3 minutes\n";
echo "  • Tests de longueur + complexité (majuscule, chiffre, caractère spécial)\n";
echo "  • Relançable à chaque modification du code\n";
echo "  • Aucune pollution de la BDD pour les tests d'intégration\n\n";

echo "💡 C'est pour ça que les tests automatiques sont standards dans l'industrie !\n\n";

