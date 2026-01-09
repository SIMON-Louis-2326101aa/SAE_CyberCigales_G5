# Documentation des Tests - SAE CyberCigales G5

## 📋 Table des matières

1. [Introduction](#introduction)
2. [Installation et Configuration](#installation-et-configuration)
3. [Architecture des Tests](#architecture-des-tests)
4. [Tests Unitaires](#tests-unitaires)
5. [Tests d'Intégration](#tests-dintégration)
6. [Exécution des Tests](#exécution-des-tests)
7. [Bonnes Pratiques](#bonnes-pratiques)
8. [Ajout de Nouveaux Tests](#ajout-de-nouveaux-tests)

---

## Introduction

Ce projet utilise **PHPUnit 9.5** pour les tests automatisés. Nous avons mis en place deux types de tests :

- **Tests Unitaires** : Testent la logique métier de manière isolée (sans dépendances externes)
- **Tests d'Intégration** : Testent l'interaction avec la base de données réelle (avec rollback automatique)

### 🎯 Objectifs

- ✅ Garantir la qualité du code
- ✅ Détecter les régressions rapidement
- ✅ Documenter le comportement attendu
- ✅ Faciliter la maintenance et les évolutions

---

## Installation et Configuration

### Prérequis

- PHP 8.0 ou supérieur
- Composer
- Base de données MySQL/MariaDB configurée

### Installation de PHPUnit

PHPUnit est déjà installé en dépendance de développement :

```bash
composer install
```

### Configuration

#### `phpunit.xml`

Fichier de configuration principal de PHPUnit :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php"
         colors="true"
         verbose="true">
    <testsuites>
        <testsuite name="Unit Tests">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration Tests">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

#### `composer.json` - Autoload

```json
{
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    }
}
```

Après toute modification, régénérer l'autoloader :

```bash
composer dump-autoload
```

---

## Architecture des Tests

### Structure des Dossiers

```
tests/
├── Unit/                          # Tests unitaires (logique isolée)
│   ├── EmailVerificationModelTest.php
│   ├── PasswordValidationTest.php
│   └── MailerTest.php
│
├── Integration/                   # Tests d'intégration (avec BDD)
│   ├── DatabaseTestCase.php      # Classe de base abstraite
│   ├── EmailVerificationModelIntegrationTest.php
│   ├── UserModelIntegrationTest.php
│   └── PasswordResetModelIntegrationTest.php
│
└── README.md                      # Documentation
```

### Conventions de Nommage

| Type | Convention | Exemple |
|------|-----------|---------|
| Fichier de test | `*Test.php` | `UserModelTest.php` |
| Classe de test | `*Test` | `class UserModelTest` |
| Méthode de test | `test*` | `testUserCreation()` |
| Namespace | `Tests\Unit` ou `Tests\Integration` | `namespace Tests\Unit;` |

---

## Tests Unitaires

### Principe

Les tests unitaires testent **une seule unité de code** de manière **isolée**, sans dépendances externes (pas de base de données, pas d'API, etc.).

### Exemple : Test de Validation de Mot de Passe

```php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

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
}
```

### Structure d'un Test (AAA Pattern)

```php
public function testSomething(): void
{
    // 1. ARRANGE (Préparer)
    $input = 'test data';
    
    // 2. ACT (Agir)
    $result = doSomething($input);
    
    // 3. ASSERT (Vérifier)
    $this->assertEquals('expected', $result);
}
```

### Assertions Courantes

```php
// Égalité
$this->assertEquals($expected, $actual);
$this->assertNotEquals($expected, $actual);

// Type
$this->assertIsString($value);
$this->assertIsArray($value);
$this->assertIsBool($value);

// Booléens
$this->assertTrue($condition);
$this->assertFalse($condition);

// Nullité
$this->assertNull($value);
$this->assertNotNull($value);

// Vide
$this->assertEmpty($value);
$this->assertNotEmpty($value);

// Regex
$this->assertMatchesRegularExpression('/pattern/', $string);

// Comparaisons numériques
$this->assertGreaterThan(5, $actual);
$this->assertLessThan(10, $actual);
```

### Data Providers

Les Data Providers permettent de tester plusieurs cas avec la même logique :

```php
/**
 * @dataProvider emailProvider
 */
public function testEmailValidation(string $email, bool $expected): void
{
    $isValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    $this->assertEquals($expected, $isValid);
}

public function emailProvider(): array
{
    return [
        'valid email' => ['user@example.com', true],
        'invalid no @' => ['userexample.com', false],
        'valid with subdomain' => ['user@mail.example.com', true],
    ];
}
```

---

## Tests d'Intégration

### Principe

Les tests d'intégration testent **l'interaction entre plusieurs composants**, notamment avec la **base de données réelle**.

### 🔒 Sécurité : Système de Transactions

**Point crucial** : Les tests d'intégration utilisent la vraie base de données **MAIS** toutes les modifications sont **annulées automatiquement** après chaque test.

#### Comment ça fonctionne ?

```php
// DatabaseTestCase.php (classe de base)

protected function setUp(): void
{
    parent::setUp();
    
    // 1. Se connecter à la base de données
    $this->db = ConnectionDB::getInstance();
    $this->pdo = $this->db->getPdo();
    
    // 2. DÉMARRER UNE TRANSACTION
    $this->pdo->beginTransaction();
}

protected function tearDown(): void
{
    // 3. ANNULER TOUTES LES MODIFICATIONS (ROLLBACK)
    if ($this->pdo->inTransaction()) {
        $this->pdo->rollBack();
    }
    
    parent::tearDown();
}
```

#### Résultat

- ✅ Les tests peuvent créer, modifier, supprimer des données
- ✅ Après chaque test, **TOUT est annulé** automatiquement
- ✅ La base de données reste **100% propre**
- ✅ Aucune pollution de données
- ✅ Tests reproductibles à l'infini

### DatabaseTestCase : Classe de Base

Tous les tests d'intégration héritent de `DatabaseTestCase` :

```php
namespace Tests\Integration;

use Tests\Integration\DatabaseTestCase;

class UserModelIntegrationTest extends DatabaseTestCase
{
    private UserModel $model;
    
    protected function setUp(): void
    {
        parent::setUp();  // Important : appelle le setUp parent (transaction)
        $this->model = new UserModel();
    }
    
    public function testSomething(): void
    {
        // Vos tests ici
        // Toutes les modifications seront annulées automatiquement !
    }
}
```

### Helpers Disponibles

`DatabaseTestCase` fournit des méthodes utiles :

```php
// Vider une table (dans la transaction)
$this->truncateTable('users');

// Compter les lignes d'une table
$count = $this->countRowsInTable('users');
$this->assertEquals(0, $count);

// Récupérer la dernière ligne insérée
$lastUser = $this->getLastInsertedRow('users');
$this->assertIsArray($lastUser);
```

### Exemple Complet : Test d'Intégration

```php
namespace Tests\Integration;

use SAE_CyberCigales_G5\Modules\model\UserModel;

/**
 * @group integration
 * @group database
 */
class UserModelIntegrationTest extends DatabaseTestCase
{
    private UserModel $model;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new UserModel();
    }
    
    /**
     * Teste la création d'un utilisateur
     */
    public function testCreateUserAfterVerificationInsertsNewRecord(): void
    {
        // ARRANGE : Préparer les données de test
        $nom = 'Dupont';
        $prenom = 'Jean';
        $email = 'jean.dupont@example.com';
        $password = password_hash('Password123!', PASSWORD_DEFAULT);
        
        // Insérer une inscription en attente
        $stmt = $this->pdo->prepare(
            'INSERT INTO pending_registrations (nom, prenom, email, password) 
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$nom, $prenom, $email, $password]);
        
        // ACT : Créer l'utilisateur
        $result = $this->model->createUserAfterVerification($email);
        
        // ASSERT : Vérifier le résultat
        $this->assertTrue($result);
        
        // Vérifier en base de données
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
        $this->assertEquals($nom, $user['nom']);
        
        // À la fin de ce test, ROLLBACK automatique !
        // L'utilisateur n'existera plus en base de données.
    }
}
```

### Tests Disponibles

#### EmailVerificationModelIntegrationTest (6 tests)
- Génération et stockage de codes à 6 chiffres
- Vérification de statuts (valid/expired/incorrect)
- Stockage d'inscriptions en attente
- Gestion du TTL (Time To Live)

#### UserModelIntegrationTest (6 tests)
- Recherche d'utilisateurs par email
- Création d'utilisateurs après vérification
- Authentification (succès/échec)
- Vérification d'existence d'email

#### PasswordResetModelIntegrationTest (6 tests)
- Création de tokens sécurisés (64 caractères hex)
- Validation de tokens
- Gestion d'expiration (TTL)
- Remplacement de tokens existants

---

## Exécution des Tests

### Commandes de Base

```bash
# Exécuter TOUS les tests (unitaires + intégration)
vendor\bin\phpunit

# Tests unitaires seulement
vendor\bin\phpunit --testsuite="Unit Tests"

# Tests d'intégration seulement
vendor\bin\phpunit --testsuite="Integration Tests"
```

### Filtres et Groupes

```bash
# Exécuter les tests d'un groupe spécifique
vendor\bin\phpunit --group integration
vendor\bin\phpunit --group database
vendor\bin\phpunit --group password

# Exécuter un fichier de test spécifique
vendor\bin\phpunit tests/Unit/PasswordValidationTest.php

# Exécuter une méthode de test spécifique
vendor\bin\phpunit --filter testPasswordMinimumLength
```

### Options Utiles

```bash
# Mode verbeux (plus de détails)
vendor\bin\phpunit --verbose

# Arrêter au premier échec
vendor\bin\phpunit --stop-on-failure

# Afficher uniquement les erreurs
vendor\bin\phpunit --stop-on-error

# Générer un rapport de couverture HTML (nécessite Xdebug)
vendor\bin\phpunit --coverage-html coverage/
```

### Sortie Typique

```
PHPUnit 9.6.29 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.0.26
Configuration: C:\...\phpunit.xml

........................................                          40 / 40 (100%)

Time: 00:06.850, Memory: 6.00 MB

OK (40 tests, 76 assertions)
```

---

## Bonnes Pratiques

### 1. Un Test = Un Concept

❌ **Mauvais** : Test qui vérifie trop de choses
```php
public function testUser(): void
{
    $user = createUser();
    $this->assertNotNull($user);
    $this->assertTrue($user->isActive());
    $this->assertEquals('admin', $user->getRole());
    // ... 10 autres assertions
}
```

✅ **Bon** : Tests séparés et ciblés
```php
public function testUserIsCreated(): void
{
    $user = createUser();
    $this->assertNotNull($user);
}

public function testNewUserIsActive(): void
{
    $user = createUser();
    $this->assertTrue($user->isActive());
}

public function testDefaultRoleIsAdmin(): void
{
    $user = createUser();
    $this->assertEquals('admin', $user->getRole());
}
```

### 2. Noms de Tests Descriptifs

❌ **Mauvais**
```php
public function testPassword(): void
public function test1(): void
```

✅ **Bon**
```php
public function testPasswordMustBeAtLeast8Characters(): void
public function testPasswordMustContainSpecialCharacter(): void
```

### 3. Utiliser les Data Providers

Au lieu de copier-coller du code :

❌ **Mauvais**
```php
public function testEmailValid1(): void {
    $this->assertTrue(isValidEmail('test@example.com'));
}
public function testEmailValid2(): void {
    $this->assertTrue(isValidEmail('user@mail.com'));
}
// ... etc
```

✅ **Bon**
```php
/**
 * @dataProvider validEmailProvider
 */
public function testEmailIsValid(string $email): void {
    $this->assertTrue(isValidEmail($email));
}

public function validEmailProvider(): array {
    return [
        ['test@example.com'],
        ['user@mail.com'],
        ['admin@site.org'],
    ];
}
```

### 4. Ordre des Assertions

Toujours mettre la **valeur attendue en premier** :

❌ **Mauvais** (confus)
```php
$this->assertEquals($actual, 'expected value');
```

✅ **Bon** (clair)
```php
$this->assertEquals('expected value', $actual);
```

### 5. Tests d'Intégration : Toujours hériter de DatabaseTestCase

❌ **Mauvais** : Pollution de la base de données
```php
class MyTest extends TestCase
{
    public function testSomething(): void
    {
        $pdo->exec("INSERT INTO users ...");
        // ❌ Ces données resteront en base !
    }
}
```

✅ **Bon** : Rollback automatique
```php
class MyTest extends DatabaseTestCase  // ← Important !
{
    public function testSomething(): void
    {
        $this->pdo->exec("INSERT INTO users ...");
        // ✅ Annulé automatiquement après le test
    }
}
```

### 6. Documenter les Tests

Utilisez des commentaires PHPDoc :

```php
/**
 * Teste que l'authentification échoue avec un mauvais mot de passe
 * 
 * Ce test vérifie le comportement de sécurité : un utilisateur ne peut
 * pas se connecter avec un mot de passe incorrect.
 * 
 * @group security
 * @group authentication
 */
public function testAuthenticationFailsWithWrongPassword(): void
{
    // ...
}
```

---

## Ajout de Nouveaux Tests

### Créer un Test Unitaire

1. **Créer le fichier** dans `tests/Unit/`

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests pour la classe MonComposant
 * 
 * @group unit
 */
class MonComposantTest extends TestCase
{
    /**
     * Teste que...
     */
    public function testQuelqueChose(): void
    {
        // ARRANGE
        $input = 'test';
        
        // ACT
        $result = monComposant($input);
        
        // ASSERT
        $this->assertEquals('expected', $result);
    }
}
```

2. **Exécuter le test**

```bash
vendor\bin\phpunit tests/Unit/MonComposantTest.php
```

### Créer un Test d'Intégration

1. **Créer le fichier** dans `tests/Integration/`

```php
<?php

namespace Tests\Integration;

use SAE_CyberCigales_G5\Modules\model\MonModel;

/**
 * Tests d'intégration pour MonModel
 * 
 * @group integration
 * @group database
 */
class MonModelIntegrationTest extends DatabaseTestCase  // ← Hériter de DatabaseTestCase
{
    private MonModel $model;
    
    protected function setUp(): void
    {
        parent::setUp();  // ← Important !
        $this->model = new MonModel();
    }
    
    /**
     * Teste que...
     */
    public function testQuelqueChose(): void
    {
        // Utiliser $this->pdo pour les requêtes SQL directes
        $stmt = $this->pdo->prepare('INSERT INTO ...');
        $stmt->execute([...]);
        
        // Tester votre modèle
        $result = $this->model->quelqueMethode();
        
        // Vérifier
        $this->assertTrue($result);
        
        // Pas besoin de nettoyer : ROLLBACK automatique !
    }
}
```

2. **Exécuter le test**

```bash
vendor\bin\phpunit tests/Integration/MonModelIntegrationTest.php
```

### Groupes de Tests

Organiser les tests avec des annotations `@group` :

```php
/**
 * @group security
 * @group authentication
 * @group critical
 */
class SecurityTest extends TestCase
{
    // ...
}
```

Puis exécuter par groupe :

```bash
vendor\bin\phpunit --group security
vendor\bin\phpunit --group critical
```

---

## Statistiques du Projet

### État Actuel

| Métrique | Valeur |
|----------|--------|
| **Total Tests** | 40 |
| **Tests Unitaires** | 22 |
| **Tests d'Intégration** | 18 |
| **Assertions** | 76 |
| **Taux de Réussite** | 100% ✅ |
| **Temps d'Exécution** | ~7 secondes |

### Couverture par Module

| Module | Tests Unitaires | Tests Intégration | Total |
|--------|----------------|-------------------|-------|
| EmailVerificationModel | 6 | 6 | 12 |
| UserModel | 0 | 6 | 6 |
| PasswordResetModel | 0 | 6 | 6 |
| Validation Passwords | 10 | 0 | 10 |
| Validation Emails | 6 | 0 | 6 |

---

## Dépannage

### Problème : Tests d'intégration échouent

**Erreur** : `Connection refused` ou `Access denied`

**Solution** : Vérifier le fichier `config/.env` :

```env
DB_HOST=localhost
DB_NAME=nom_base
DB_USER=utilisateur
DB_PASSWORD=mot_de_passe
```

### Problème : Classe DatabaseTestCase non trouvée

**Erreur** : `Class 'Tests\Integration\DatabaseTestCase' not found`

**Solution** : Régénérer l'autoloader :

```bash
composer dump-autoload
```

### Problème : Les tests laissent des données en base

**Cause** : Vous n'héritez pas de `DatabaseTestCase`

**Solution** : Toujours hériter de `DatabaseTestCase` pour les tests d'intégration :

```php
class MonTest extends DatabaseTestCase  // ← Pas TestCase !
```

---

## Ressources

### Documentation Officielle

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [PHPUnit Assertions](https://phpunit.de/manual/current/en/assertions.html)
- [PHPUnit Annotations](https://phpunit.de/manual/current/en/annotations.html)

### Tutoriels

- [Test-Driven Development (TDD)](https://en.wikipedia.org/wiki/Test-driven_development)
- [AAA Pattern (Arrange-Act-Assert)](https://automationpanda.com/2020/07/07/arrange-act-assert-a-pattern-for-writing-good-tests/)

### Commandes Utiles

```bash
# Installer les dépendances
composer install

# Régénérer l'autoloader
composer dump-autoload

# Exécuter tous les tests
vendor\bin\phpunit

# Exécuter les tests avec verbosité
vendor\bin\phpunit --verbose

# Voir la liste des groupes disponibles
vendor\bin\phpunit --list-groups

# Générer un rapport de couverture (nécessite Xdebug)
vendor\bin\phpunit --coverage-html coverage/
```

---

## Conclusion

Les tests automatisés sont **essentiels** pour garantir la qualité et la fiabilité du code. Avec cette configuration :

- ✅ Tests unitaires pour la logique métier
- ✅ Tests d'intégration sécurisés (transactions + rollback)
- ✅ Aucune pollution de la base de données
- ✅ Exécution rapide (~7 secondes pour 40 tests)
- ✅ Documentation complète

**N'oubliez pas** : Un code sans tests est un code à risque ! 🎯

---

*Documentation générée pour le projet SAE CyberCigales G5*  
*Dernière mise à jour : Novembre 2025*

