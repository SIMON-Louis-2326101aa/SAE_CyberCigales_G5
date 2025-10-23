# Fonctionnalité de Limitation des Tentatives de Connexion

## Description
Cette fonctionnalité implémente un système de sécurité pour limiter les tentatives de connexion échouées et protéger contre les attaques par force brute.

**⚡ Utilise les SESSIONS PHP** - Aucune modification de base de données nécessaire !

## Fonctionnalités

### Limitation par Email
- **Maximum de tentatives** : 5 tentatives par email
- **Durée de blocage** : 15 minutes
- **Période de comptage** : 15 minutes glissantes

### Limitation par IP
- **Maximum de tentatives** : 10 tentatives par adresse IP
- **Durée de blocage** : 30 minutes
- **Période de comptage** : 15 minutes glissantes

## Fichiers Modifiés/Créés

### Nouveaux Fichiers
1. **`Modules/model/loginAttemptModel.php`** - Modèle pour gérer les tentatives (utilise les sessions)
2. **`RATE_LIMITING_README.md`** - Cette documentation

### Fichiers Modifiés
1. **`Modules/controller/userController.php`** - Intégration du système de rate limiting
2. **`Modules/view/formConnectionView.php`** - Affichage des messages d'erreur amélioré

## Installation

### ✅ Aucune installation nécessaire !
Le système utilise les **sessions PHP** - aucune modification de base de données requise.
Tout fonctionne automatiquement dès que le code est déployé.

## Utilisation

### Messages d'erreur affichés
- **Tentatives restantes** : "Il vous reste X tentative(s) avant le blocage temporaire"
- **Compte bloqué** : "Trop de tentatives de connexion échouées. Votre compte est temporairement bloqué. Veuillez réessayer dans X minute(s)."
- **IP bloquée** : "Trop de tentatives de connexion depuis cette adresse IP. Veuillez réessayer dans X minute(s)."

### Nettoyage automatique
Le système nettoie automatiquement les tentatives expirées à chaque vérification.
Les données sont stockées en **session** et disparaissent naturellement après :
- Le temps de blocage (15 minutes)
- La fermeture du navigateur
- La connexion réussie de l'utilisateur

✅ **Aucune maintenance requise !**

## Sécurité

### Protection contre les attaques
- **Force brute** : Limitation du nombre de tentatives
- **Attaques distribuées** : Limitation par IP
- **Tentatives persistantes** : Blocage temporaire

### Données stockées (en session PHP)
- Email de la tentative
- Timestamps des tentatives
- Compteur de tentatives
- Aucun mot de passe ou données sensibles
- **Avantage** : Les données disparaissent automatiquement avec la session

## Configuration Avancée

### Modifier les limites
Dans `Modules/model/loginAttemptModel.php`, vous pouvez modifier :

```php
// Limitation par email
private const MAX_ATTEMPTS = 5; // Nombre maximum de tentatives
private const BLOCK_DURATION = 15; // Durée de blocage en minutes
```

### Période de comptage
```php
// Dans les méthodes getFailedAttemptsCount()
$minutes = 15; // Période de comptage en minutes
```

## Dépannage

### Problèmes courants
1. **Sessions non démarrées** : Vérifiez que `session_start()` est appelé (déjà fait automatiquement)
2. **Messages d'erreur non affichés** : Vérifiez que les variables sont passées correctement à la vue
3. **Blocage permanent** : Le blocage se lève automatiquement après 15 minutes

### Débogage
Pour déboguer, vous pouvez afficher le contenu de la session :

```php
echo '<pre>';
print_r($_SESSION['login_attempts']);
echo '</pre>';
```

## Support
Pour toute question ou problème, consultez la documentation du projet ou contactez l'équipe de développement.
