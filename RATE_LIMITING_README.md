# Fonctionnalité de Limitation des Tentatives de Connexion

## Description
Cette fonctionnalité implémente un système de sécurité pour limiter les tentatives de connexion échouées et protéger contre les attaques par force brute.

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
1. **`Modules/model/loginAttemptModel.php`** - Modèle pour gérer les tentatives de connexion
2. **`database_setup.sql`** - Script SQL pour créer la table `login_attempts`
3. **`setup_database.php`** - Script automatique de configuration
4. **`includes/cleanupLoginAttempts.php`** - Script de nettoyage des anciennes tentatives
5. **`RATE_LIMITING_README.md`** - Cette documentation

### Fichiers Modifiés
1. **`Modules/controller/userController.php`** - Intégration du système de rate limiting
2. **`Modules/view/formConnectionView.php`** - Affichage des messages d'erreur amélioré

## Installation

### 1. Créer la table de base de données
Exécutez le script SQL `database_setup.sql` dans votre base de données :

```sql
-- Copier le contenu de database_setup.sql dans votre outil de gestion de base de données
```

### 2. Configuration automatique
Le système se configure automatiquement lors de l'utilisation. Aucune configuration supplémentaire n'est requise.

## Utilisation

### Messages d'erreur affichés
- **Tentatives restantes** : "Il vous reste X tentative(s) avant le blocage temporaire"
- **Compte bloqué** : "Trop de tentatives de connexion échouées. Votre compte est temporairement bloqué. Veuillez réessayer dans X minute(s)."
- **IP bloquée** : "Trop de tentatives de connexion depuis cette adresse IP. Veuillez réessayer dans X minute(s)."

### Nettoyage automatique
Le système nettoie automatiquement les tentatives de plus de 24 heures. Pour un nettoyage périodique, configurez un cron job :

```bash
# Exécuter tous les jours à 2h du matin
0 2 * * * /usr/bin/php /chemin/vers/votre/projet/includes/cleanupLoginAttempts.php
```

## Sécurité

### Protection contre les attaques
- **Force brute** : Limitation du nombre de tentatives
- **Attaques distribuées** : Limitation par IP
- **Tentatives persistantes** : Blocage temporaire

### Données stockées
- Email de la tentative
- Adresse IP
- Timestamp de la tentative
- Aucun mot de passe ou données sensibles

## Configuration Avancée

### Modifier les limites
Dans `Modules/model/loginAttemptModel.php`, vous pouvez modifier :

```php
// Limitation par email
$maxAttempts = 5; // Nombre maximum de tentatives
$blockDuration = 15; // Durée de blocage en minutes

// Limitation par IP
$maxAttempts = 10; // Nombre maximum de tentatives par IP
$blockDuration = 30; // Durée de blocage en minutes
```

### Période de comptage
```php
// Dans les méthodes getFailedAttemptsCount()
$minutes = 15; // Période de comptage en minutes
```

## Dépannage

### Problèmes courants
1. **Table non créée** : Vérifiez que le script SQL a été exécuté
2. **Messages d'erreur non affichés** : Vérifiez que les variables sont passées correctement à la vue
3. **Blocage permanent** : Le blocage se lève automatiquement après la durée configurée

### Logs et débogage
Pour déboguer, vous pouvez consulter directement la table `login_attempts` :

```sql
SELECT * FROM login_attempts ORDER BY attempted_at DESC LIMIT 10;
```

## Support
Pour toute question ou problème, consultez la documentation du projet ou contactez l'équipe de développement.
