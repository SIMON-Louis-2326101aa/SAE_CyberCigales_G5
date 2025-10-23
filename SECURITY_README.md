# 🔒 Améliorations de Sécurité - SAE CyberCigales

## Description
Cette branche ajoute de multiples couches de sécurité pour protéger l'application contre les vulnérabilités web courantes.

---

## 🛡️ Protections Implémentées

### 1. Protection contre les Injections SQL
- ✅ **Requêtes préparées** : Déjà utilisées dans tout le code
- ✅ **Détection de patterns SQL** : Détecte les tentatives d'injection
- ✅ **Logging des tentatives** : Enregistre les tentatives suspectes

### 2. Protection contre XSS (Cross-Site Scripting)
- ✅ **Sanitisation des entrées** : Nettoyage automatique avec `htmlspecialchars`
- ✅ **Détection de patterns XSS** : Détecte les balises `<script>`, événements JS, etc.
- ✅ **En-têtes CSP** : Content Security Policy pour bloquer les scripts non autorisés
- ✅ **En-tête X-XSS-Protection** : Protection XSS du navigateur

### 3. Protection CSRF (Cross-Site Request Forgery)
- ✅ **Tokens CSRF** : Génération et validation de tokens
- ✅ **Vérification sur les actions sensibles** : Ajout de tokens dans les formulaires

### 4. Sécurité des Sessions
- ✅ **Régénération de session** : Après connexion pour éviter la fixation
- ✅ **Configuration sécurisée** : `httponly`, `secure`, `samesite`
- ✅ **Timeout de session** : Expiration automatique

### 5. En-têtes HTTP de Sécurité
- ✅ **X-Frame-Options** : Protection contre le clickjacking
- ✅ **X-Content-Type-Options** : Empêche le MIME sniffing
- ✅ **Strict-Transport-Security** : Force HTTPS
- ✅ **Referrer-Policy** : Contrôle des informations de référence
- ✅ **Content-Security-Policy** : Politique de sécurité du contenu

### 6. Validation et Sanitisation
- ✅ **Validation d'email** : Utilisation de `filter_var`
- ✅ **Validation de nom** : Regex pour caractères autorisés
- ✅ **Validation de mot de passe** : Vérification de la complexité
- ✅ **Sanitisation automatique** : Nettoyage des entrées utilisateur

### 7. Logging de Sécurité
- ✅ **Enregistrement des événements** : Tentatives d'injection, XSS, etc.
- ✅ **Fichier de log** : `logs/security.log`
- ✅ **Informations enregistrées** : Timestamp, IP, User-Agent, détails

### 8. Middleware d'Authentification
- ✅ **Protection des pages privées** : Vérification automatique
- ✅ **Redirection automatique** : Si non connecté

---

## 📁 Fichiers Créés

### Nouveaux fichiers :
1. **`includes/securityHelper.php`** - Classe utilitaire de sécurité
2. **`includes/authMiddleware.php`** - Middleware d'authentification
3. **`logs/.gitkeep`** - Conserve le dossier logs dans Git
4. **`SECURITY_README.md`** - Cette documentation

### Fichiers modifiés :
1. **`public/index.php`** - Ajout des en-têtes de sécurité
2. **`.gitignore`** - Ignore les fichiers de logs

---

## 🔧 Utilisation

### Sanitisation des Entrées

```php
require_once __DIR__ . '/includes/securityHelper.php';

// Nettoyer une chaîne
$nom = SecurityHelper::sanitizeString($_POST['nom']);

// Valider et nettoyer un email
$email = SecurityHelper::sanitizeEmail($_POST['email']);
if ($email === false) {
    // Email invalide
}

// Valider un nom
if (!SecurityHelper::validateName($nom)) {
    // Nom invalide
}

// Nettoyer un tableau complet (sauf mots de passe)
$data = SecurityHelper::sanitizeInput($_POST, ['pwd', 'confirm_pwd']);
```

### Protection CSRF

```php
// Dans le contrôleur - générer un token
$csrfToken = SecurityHelper::generateCSRFToken();

// Dans la vue - ajouter le token au formulaire
<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

// Dans le contrôleur - vérifier le token
if (!SecurityHelper::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    die('Token CSRF invalide');
}
```

### Validation de Mot de Passe

```php
$result = SecurityHelper::validatePassword($password);

if (!$result['valid']) {
    // Afficher les erreurs
    foreach ($result['errors'] as $error) {
        echo $error . "<br>";
    }
}
```

### Protection des Pages

```php
// Protéger une page (nécessite connexion)
SecurityHelper::requireLogin();

// Vérifier si connecté
if (SecurityHelper::isLoggedIn()) {
    // Utilisateur connecté
}

// Régénérer la session (après connexion)
SecurityHelper::regenerateSession();
```

### Détection d'Attaques

```php
// Détecter injection SQL
if (SecurityHelper::detectSQLInjection($input)) {
    // Tentative d'injection détectée et loguée
}

// Détecter XSS
if (SecurityHelper::detectXSS($input)) {
    // Tentative de XSS détectée et loguée
}
```

---

## 📊 Logs de Sécurité

Les événements de sécurité sont enregistrés dans `logs/security.log` :

```
[2025-10-23 14:30:15] SQL Injection Attempt - IP: 192.168.1.100 - User-Agent: Mozilla/5.0... - Details: SELECT * FROM users
[2025-10-23 14:35:22] XSS Attempt - IP: 192.168.1.101 - User-Agent: Chrome/... - Details: <script>alert('XSS')</script>
```

### Analyser les logs

```bash
# Voir les dernières tentatives d'attaque
tail -n 50 logs/security.log

# Rechercher des IP spécifiques
grep "192.168.1.100" logs/security.log

# Compter les tentatives d'injection SQL
grep "SQL Injection" logs/security.log | wc -l
```

---

## 🎯 Bonnes Pratiques

### 1. **Toujours sanitiser les entrées utilisateur**
```php
$nom = SecurityHelper::sanitizeString($_POST['nom']);
```

### 2. **Utiliser les requêtes préparées**
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
```

### 3. **Valider les données côté serveur**
Ne jamais faire confiance uniquement à la validation JavaScript.

### 4. **Utiliser HTTPS en production**
Les cookies `secure` nécessitent HTTPS.

### 5. **Protéger les pages sensibles**
```php
SecurityHelper::requireLogin();
```

### 6. **Ajouter des tokens CSRF**
Sur tous les formulaires modifiant des données.

### 7. **Surveiller les logs**
Vérifier régulièrement `logs/security.log`.

---

## ⚠️ Points d'Attention

### Configuration en Production

1. **HTTPS** : Obligatoire pour les cookies secure
2. **Logs** : Surveiller et analyser régulièrement
3. **Permissions** : Le dossier `logs/` doit être en écriture
4. **CSP** : Adapter la Content Security Policy selon vos besoins

### Limitations

- La détection d'injection SQL est basique (patterns simples)
- Les requêtes préparées restent la meilleure protection
- Le logging peut ralentir l'application si trop d'attaques

---

## 🔐 Checklist de Sécurité

- [x] Requêtes préparées pour toutes les interactions DB
- [x] Sanitisation des entrées utilisateur
- [x] Validation des emails et noms
- [x] Validation des mots de passe
- [x] Protection CSRF sur les formulaires
- [x] En-têtes HTTP de sécurité
- [x] Sessions sécurisées
- [x] Logging des événements suspects
- [x] Protection des pages privées
- [x] Détection XSS et SQL injection
- [ ] Tests de pénétration
- [ ] Audit de sécurité complet

---

## 📚 Ressources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)

---

✨ **Application sécurisée !** Continuez à suivre les bonnes pratiques de sécurité ! 🔒
