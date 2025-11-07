# Explication des Tests - Pour l'Équipe

## 🎯 Objectif de cette tâche

J'ai mis en place un **système de tests automatisés** pour le projet. C'est comme avoir un robot qui vérifie automatiquement que notre code fonctionne correctement.

---

## 📚 Les 3 concepts clés à comprendre

### 1️⃣ Qu'est-ce qu'un test automatisé ?

**En gros** : C'est du code qui teste notre code.

**Exemple concret** :
```php
// Notre code
function addition($a, $b) {
    return $a + $b;
}

// Le test
public function testAddition() {
    $resultat = addition(2, 3);
    $this->assertEquals(5, $resultat);  // Vérifie que 2+3 = 5
}
```

**Pourquoi c'est utile ?**
- ✅ On détecte les bugs automatiquement
- ✅ Si on modifie le code, on sait tout de suite si on a cassé quelque chose
- ✅ C'est plus rapide que de tout tester à la main

---

### 2️⃣ Les 2 types de tests qu'on a mis en place

#### A) Tests Unitaires (22 tests)

**C'est quoi ?** On teste des petits bouts de code isolés, sans toucher à la base de données.

**Exemples dans notre projet :**

1. **Validation de mots de passe**
   ```php
   // On teste : "Un mot de passe doit avoir au moins 8 caractères"
   testPasswordMinimumLength("Pass1!", false)  // Trop court → doit échouer
   testPasswordMinimumLength("Password1!", true)  // Assez long → doit réussir
   ```

2. **Validation d'emails**
   ```php
   // On teste : "Un email doit avoir un @"
   testEmail("test@example.com", true)   // Valide
   testEmail("test.example.com", false)  // Invalide (pas de @)
   ```

**Avantage** : Super rapide (0.03 secondes pour 22 tests)

---

#### B) Tests d'Intégration (18 tests)

**C'est quoi ?** On teste l'interaction avec la vraie base de données.

**LA QUESTION IMPORTANTE** : "Mais si on teste avec la vraie BDD, on va polluer les données ?"

**MA RÉPONSE** : Non ! Et c'est ça l'astuce géniale ! 🎉

---

### 3️⃣ Le système de transactions (LA PARTIE IMPORTANTE)

**Le problème** : Si je crée un utilisateur de test en base de données, il va rester après le test.

**La solution** : Les transactions SQL

#### Comment ça marche ? (Explication simple)

```
┌─────────────────────────────────────┐
│ AVANT LE TEST                       │
│ → BEGIN TRANSACTION                 │  ← On dit "commence à enregistrer"
└─────────────────────────────────────┘
           ↓
┌─────────────────────────────────────┐
│ PENDANT LE TEST                     │
│ → INSERT INTO users ...             │  ← On fait nos modifications
│ → UPDATE ...                         │
│ → DELETE ...                         │
└─────────────────────────────────────┘
           ↓
┌─────────────────────────────────────┐
│ APRÈS LE TEST                       │
│ → ROLLBACK                          │  ← On dit "annule tout !"
└─────────────────────────────────────┘
```

**Résultat** : Toutes les modifications faites pendant le test sont annulées automatiquement.

**Analogie** : C'est comme un brouillon. Tu écris dessus, tu testes, puis tu jettes le brouillon. L'original n'a jamais été touché !

---

## 🛠️ Ce que j'ai installé et pourquoi

### 1. PHPUnit (l'outil de tests)

**C'est quoi ?** Une librairie PHP qui permet d'écrire et d'exécuter des tests.

**Installation** :
```bash
composer require --dev phpunit/phpunit
```

Le `--dev` signifie : "C'est juste pour le développement, pas pour la production"

---

### 2. Structure des dossiers

```
tests/
├── Unit/              ← Tests rapides, sans BDD
│   ├── PasswordValidationTest.php
│   ├── MailerTest.php
│   └── EmailVerificationModelTest.php
│
└── Integration/       ← Tests avec BDD (+ transactions)
    ├── DatabaseTestCase.php          ← LA CLASSE MAGIQUE
    ├── UserModelIntegrationTest.php
    ├── EmailVerificationModelIntegrationTest.php
    └── PasswordResetModelIntegrationTest.php
```

---

### 3. La classe DatabaseTestCase (le cœur du système)

**C'est quoi ?** Une classe de base que tous les tests d'intégration héritent.

**Son rôle** : Gérer automatiquement les transactions.

```php
class DatabaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        // Exécuté AVANT chaque test
        $this->pdo->beginTransaction();  // ← Démarre la transaction
    }
    
    protected function tearDown(): void
    {
        // Exécuté APRÈS chaque test
        $this->pdo->rollBack();  // ← Annule tout !
    }
}
```

**Comment on l'utilise ?**

```php
// Tous nos tests d'intégration héritent de DatabaseTestCase
class UserModelIntegrationTest extends DatabaseTestCase  // ← Important !
{
    public function testCreateUser()
    {
        // Ici je peux faire ce que je veux en BDD
        // Tout sera annulé automatiquement après !
    }
}
```

---

## 📊 Ce qui a été testé (exemples concrets)

### Tests Unitaires

| Ce qu'on teste | Exemple |
|----------------|---------|
| Longueur mot de passe | "Pass1!" → ❌ trop court |
| Complexité mot de passe | "password123" → ❌ pas de majuscule |
| Format email | "test@example.com" → ✅ valide |
| Code de vérification | Doit être 6 chiffres |

### Tests d'Intégration

| Ce qu'on teste | Exemple |
|----------------|---------|
| Créer un utilisateur | INSERT → vérifie que l'user est bien créé → ROLLBACK |
| Authentification | Login avec bon/mauvais mot de passe |
| Générer un code de vérification | Stockage en BDD → vérification → ROLLBACK |
| Token mot de passe oublié | Génération token → vérification validité → ROLLBACK |

---

## 🎓 Comment expliquer ça à l'équipe ?

### Version courte (2 minutes)

> "J'ai installé PHPUnit pour automatiser les tests. On a 40 tests :
> - 22 tests unitaires rapides (validation de données)
> - 18 tests d'intégration avec la BDD
> 
> Pour les tests d'intégration, j'utilise des transactions SQL : 
> avant chaque test → BEGIN, après chaque test → ROLLBACK.
> Donc la BDD reste propre, aucune donnée de test ne reste."

### Version moyenne (5 minutes)

1. **Montrer un test unitaire simple**
   ```php
   public function testPasswordTooShort() {
       $password = "Pass1!";
       $isValid = strlen($password) >= 8;
       $this->assertFalse($isValid);  // Doit échouer car < 8 chars
   }
   ```
   
2. **Expliquer le problème des tests d'intégration**
   > "Si je teste la création d'utilisateur en BDD, l'utilisateur va rester après. 
   > C'est gênant car ça pollue les données."

3. **Expliquer la solution**
   > "J'utilise BEGIN TRANSACTION avant le test et ROLLBACK après.
   > C'est comme CTRL+Z : toutes les modifications sont annulées."

4. **Montrer les résultats**
   ```bash
   vendor\bin\phpunit
   
   OK (40 tests, 76 assertions) ✅
   ```

### Version longue (10-15 minutes)

1. Expliquer pourquoi les tests sont importants
2. Montrer la structure des dossiers
3. Faire une démo en live :
   - Lancer les tests → tout est vert ✅
   - Casser volontairement quelque chose → le test devient rouge ❌
   - Réparer → redevient vert ✅
4. Expliquer DatabaseTestCase en détail
5. Montrer un test d'intégration
6. Vérifier en BDD qu'il n'y a pas de données de test

---

## 🔑 Les points clés à retenir

1. **PHPUnit** = outil pour écrire et lancer des tests
2. **Tests unitaires** = rapides, sans BDD
3. **Tests d'intégration** = avec BDD mais avec transactions (ROLLBACK auto)
4. **40 tests au total**, tous passent ✅
5. **0 pollution de la BDD** grâce aux transactions

---

## 💡 Démonstration pratique (à faire avec l'équipe)

### Étape 1 : Montrer que ça fonctionne
```bash
cd C:\Users\majha\Desktop\projet SAE\SAE_CyberCigales_G5
git checkout feature/unit-tests
composer install
vendor\bin\phpunit
```

Résultat attendu : `OK (40 tests, 76 assertions)`

---

### Étape 2 : Prouver que la BDD reste propre

```bash
# Avant les tests : compter les utilisateurs
SELECT COUNT(*) FROM users;  -- Ex: 5 utilisateurs

# Lancer les tests
vendor\bin\phpunit --testsuite="Integration Tests"

# Après les tests : recompter
SELECT COUNT(*) FROM users;  -- Toujours 5 ! Aucune pollution
```

---

### Étape 3 : Montrer un test qui échoue

Modifier un test pour qu'il échoue volontairement :

```php
// Dans tests/Unit/PasswordValidationTest.php
public function testPasswordMinimumLength() {
    $this->assertEquals(false, true);  // ← Toujours faux !
}
```

Relancer : `vendor\bin\phpunit`

Résultat : ❌ FAILURES! Le test échoue comme prévu.

Remettre le bon code, relancer : ✅ OK

---

## 📖 Ressources pour approfondir

Si vos camarades veulent en savoir plus :

1. **PHPUnit docs** : https://phpunit.de/documentation.html
2. **Transactions SQL** : https://dev.mysql.com/doc/refman/8.0/en/commit.html
3. **Notre doc complète** : `TESTS_DOCUMENTATION.md` (853 lignes)

---

## ❓ Questions fréquentes de l'équipe

### Q1 : "Pourquoi pas juste tester à la main ?"
**R** : Tester à la main = 30 minutes à chaque modification. Tests auto = 7 secondes.

### Q2 : "C'est compliqué à maintenir ?"
**R** : Non. Quand on ajoute une feature, on ajoute un test. C'est simple.

### Q3 : "Les tests ralentissent le développement ?"
**R** : Au début oui (setup), mais après ça ACCÉLÈRE car on détecte les bugs immédiatement.

### Q4 : "Tous les projets ont des tests ?"
**R** : Tous les projets professionnels OUI. C'est un standard de l'industrie.

### Q5 : "Si on modifie la BDD, faut tout refaire ?"
**R** : Non, juste adapter les tests concernés. Souvent 5-10 minutes max.

---

## 🎯 Ce que vous pouvez dire à vos camarades

> "J'ai utilisé l'IA pour comprendre et mettre en place PHPUnit, mais maintenant 
> je comprends comment ça fonctionne :
> 
> - PHPUnit exécute nos tests automatiquement
> - Pour les tests avec BDD, j'utilise BEGIN TRANSACTION et ROLLBACK
> - Ça permet de tester sans polluer les données
> - C'est un standard dans l'industrie, on en aura besoin pour notre projet
> 
> Je peux vous faire une démo si vous voulez voir comment ça marche en live !"

---

## ✅ Check-list : "Est-ce que je comprends ?"

Cochez mentalement :

- [ ] Je sais ce qu'est PHPUnit
- [ ] Je sais la différence entre test unitaire et test d'intégration  
- [ ] Je comprends BEGIN TRANSACTION / ROLLBACK
- [ ] Je sais pourquoi DatabaseTestCase est important
- [ ] Je peux expliquer comment lancer les tests
- [ ] Je peux montrer un exemple de test simple
- [ ] Je peux prouver que la BDD reste propre

**Si vous cochez tout → vous êtes prêt à expliquer ! 🎓**

---

## 🤝 Conseil pour la suite

**Proposez à votre équipe** :

> "Maintenant qu'on a les tests en place, à chaque fois qu'on code une nouvelle 
> feature, on écrit aussi un test. Comme ça :
> 1. On est sûr que ça marche
> 2. On protège le code contre les régressions futures
> 3. On apprend tous ensemble
> 
> Je peux vous montrer comment écrire un test simple si vous voulez ?"

**Résultat** : Vous passez de "celui qui utilise l'IA" à "celui qui partage ses connaissances" 💪

---

*Dernière mise à jour : Novembre 2025*
*Document créé pour faciliter l'explication à l'équipe*

