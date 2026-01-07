# Tests Unitaires - SAE CyberCigales G5

## 📁 Structure des tests

```
tests/
├── Unit/              # Tests unitaires (logique métier isolée)
│   ├── EmailVerificationModelTest.php (3 tests)
│   └── UserModelTest.php (7 tests avec mocks)
└── Integration/       # Tests d'intégration (avec base de données)
    ├── DatabaseTestCase.php (classe de base)
    ├── EmailVerificationModelIntegrationTest.php (7 tests)
    ├── GameProgressModelIntegrationTest.php (5 tests)
    ├── PasswordResetModelIntegrationTest.php (8 tests)
    ├── PendingRegistrationModelIntegrationTest.php (5 tests)
    └── UserModelIntegrationTest.php (18 tests)
```

**Total : 49 tests (10 unitaires + 39 intégration) avec 137 assertions**

## 🚀 Exécuter les tests

> **Note Windows :** Utilisez `php vendor/phpunit/phpunit/phpunit` au lieu de `vendor/bin/phpunit`

### Tous les tests
```bash
php vendor/phpunit/phpunit/phpunit
```

### Tests unitaires seulement
```bash
php vendor/phpunit/phpunit/phpunit --testsuite="Unit Tests"
```

### Tests d'intégration seulement
```bash
php vendor/phpunit/phpunit/phpunit --testsuite="Integration Tests"
```

### Un fichier de test spécifique
```bash
php vendor/phpunit/phpunit/phpunit tests/Unit/UserModelTest.php
```

### Un test spécifique dans un fichier
```bash
php vendor/phpunit/phpunit/phpunit --filter testFindByEmailReturnsTrueWhenUserExists
```

### Avec couverture de code (nécessite Xdebug)
```bash
php vendor/phpunit/phpunit/phpunit --coverage-html coverage/
```

### Filtrer par groupe
```bash
php vendor/phpunit/phpunit/phpunit --group unit
php vendor/phpunit/phpunit/phpunit --group integration
php vendor/phpunit/phpunit/phpunit --group mock
```

### Mode verbose (détails)
```bash
php vendor/phpunit/phpunit/phpunit --verbose
```

### Arrêter au premier échec
```bash
php vendor/phpunit/phpunit/phpunit --stop-on-failure
```

## 📝 Conventions

### Nommage des tests
- Les fichiers de test doivent se terminer par `Test.php`
- Les méthodes de test doivent commencer par `test`
- Utiliser des noms descriptifs : `testFindByEmailReturnsTrueWhenUserExists`
- Ajouter `@testdox` pour des descriptions en français dans le terminal

### Structure d'un test
```php
public function testSomething(): void
{
    // Arrange (Préparer)
    $input = 'test';
    
    // Act (Agir)
    $result = doSomething($input);
    
    // Assert (Vérifier)
    $this->assertEquals('expected', $result);
}
```

### Data Providers
Pour tester plusieurs cas avec la même logique :
```php
/**
 * @dataProvider emailProvider
 */
public function testEmailValidation(string $email, bool $expected): void
{
    // test logic
}

public function emailProvider(): array
{
    return [
        'valid email' => ['user@example.com', true],
        'invalid email' => ['invalid', false],
    ];
}
```

## 🔒 Tests d'intégration et sécurité

Les tests d'intégration utilisent la **vraie base de données** mais avec un système de **transactions** :
- Chaque test démarre dans une transaction (`BEGIN TRANSACTION`)
- Toutes les modifications sont annulées automatiquement à la fin (`ROLLBACK`)
- **Aucune donnée n'est persistée** en base de données réelle

Cela permet de tester avec des données réelles sans risque de pollution de la base.

### Tests d'intégration disponibles
- ✅ UserModelIntegrationTest (18 tests)
- ✅ EmailVerificationModelIntegrationTest (7 tests)
- ✅ PasswordResetModelIntegrationTest (8 tests)
- ✅ GameProgressModelIntegrationTest (5 tests)
- ✅ PendingRegistrationModelIntegrationTest (5 tests)

**Total : 39 tests d'intégration avec transactions SQL et ROLLBACK automatique**

## 🎯 Couverture actuelle

### ✅ Tests implémentés
- ✅ Tests unitaires avec mocks (UserModel)
- ✅ Tests unitaires pour génération de codes (EmailVerificationModel)
- ✅ Tests d'intégration pour UserModel (authentification, CRUD)
- ✅ Tests d'intégration pour EmailVerificationModel
- ✅ Tests d'intégration pour PasswordResetModel
- ✅ Tests d'intégration pour GameProgressModel
- ✅ Tests d'intégration pour PendingRegistrationModel

### 📋 Améliorations futures
- [ ] Tests pour les contrôleurs (AdminController, PuzzleController)
- [ ] Tests end-to-end avec parcours utilisateur complet
- [ ] CI/CD avec GitHub Actions
- [ ] Augmenter la couverture de code (objectif : 80%+)

## 📚 Ressources

- [Documentation PHPUnit](https://phpunit.de/documentation.html)
- [Best Practices](https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html)

