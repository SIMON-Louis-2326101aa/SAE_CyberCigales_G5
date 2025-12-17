# Tests Unitaires - SAE CyberCigales G5

## 📁 Structure des tests

```
tests/
├── Unit/              # Tests unitaires (logique métier isolée)
│   ├── EmailVerificationModelTest.php
│   ├── PasswordValidationTest.php
│   └── MailerTest.php
└── Integration/       # Tests d'intégration (avec base de données)
```

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
php vendor/phpunit/phpunit/phpunit tests/Unit/PasswordValidationTest.php
```

### Un test spécifique dans un fichier
```bash
php vendor/phpunit/phpunit/phpunit --filter testPasswordMinimumLength
```

### Avec couverture de code (nécessite Xdebug)
```bash
php vendor/phpunit/phpunit/phpunit --coverage-html coverage/
```

### Filtrer par groupe
```bash
php vendor/phpunit/phpunit/phpunit --group unit
php vendor/phpunit/phpunit/phpunit --group password
php vendor/phpunit/phpunit/phpunit --group mailer
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
- Utiliser des noms descriptifs : `testPasswordMinimumLength`

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
- ✅ EmailVerificationModelIntegrationTest (6 tests)
- ✅ UserModelIntegrationTest (6 tests)
- ✅ PasswordResetModelIntegrationTest (6 tests)

## 🎯 À développer

### Tests à ajouter
- [ ] Tests pour les contrôleurs
- [ ] Tests pour les sessions et flash messages
- [ ] Tests de rate limiting
- [ ] Tests end-to-end

### Configuration à améliorer
- [ ] Mock des dépendances externes (mail, etc.)
- [ ] CI/CD avec GitHub Actions
- [ ] Tests de performance

## 📚 Ressources

- [Documentation PHPUnit](https://phpunit.de/documentation.html)
- [Best Practices](https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html)

