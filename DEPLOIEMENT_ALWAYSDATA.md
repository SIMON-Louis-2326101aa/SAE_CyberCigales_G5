# 🚀 Guide de déploiement sur AlwaysData

## 📋 Prérequis

- Compte AlwaysData actif
- Accès Git configuré
- Base de données MySQL créée sur AlwaysData

## 🔧 Configuration requise

### 1. Base de données
Les informations de connexion sont déjà configurées dans `includes/connectionDB.php` :
```php
DB_HOST: mysql-escapethecode.alwaysdata.net
DB_NAME: escapethecode_bd
DB_USER: 433487
DB_PASS: SAECyberCigales
```

### 2. Système d'email
✅ **Aucune configuration supplémentaire nécessaire !**

Le système d'envoi d'email fonctionne automatiquement sur AlwaysData grâce à :
- La fonction `mail()` PHP est préc onfigurée
- Le serveur SMTP d'AlwaysData est utilisé automatiquement
- L'adresse d'expéditeur : `no-reply@escapethecode.alwaysdata.net`

## 📤 Déploiement

### Étape 1 : Push vers GitHub
```bash
git add .
git commit -m "Système de vérification email complet"
git push origin main
```

### Étape 2 : Sur AlwaysData
1. Connectez-vous à votre compte AlwaysData
2. Allez dans **Sites** > **Installer une application**
3. Choisissez **Git** comme source
4. Entrez l'URL de votre dépôt GitHub
5. Configurez le répertoire de déploiement

### Étape 3 : Vérification
Une fois déployé, le système fonctionnera automatiquement :
- ✅ Connexion à la base de données
- ✅ Envoi d'emails de vérification
- ✅ Création automatique des tables nécessaires

## 🔍 Fonctionnalités

### Système de vérification email
1. **Inscription** : Les données sont stockées temporairement
2. **Envoi du code** : Email automatique avec code à 6 chiffres
3. **Validation** : Le compte est créé seulement après vérification
4. **Sécurité** : Codes avec expiration de 10 minutes

### Tables créées automatiquement
- `users` : Utilisateurs avec champ `email_verified`
- `email_verification_codes` : Codes de vérification temporaires
- `pending_registrations` : Inscriptions en attente de validation

## 🐛 Dépannage

### Les emails ne sont pas envoyés ?
1. Vérifiez les logs AlwaysData : **Sites** > **Logs**
2. Assurez-vous que le domaine `escapethecode.alwaysdata.net` est actif
3. Vérifiez que l'email d'expédition est autorisé

### Erreur de base de données ?
1. Vérifiez que la base de données existe
2. Vérifiez les identifiants dans `includes/connectionDB.php`
3. Les tables se créent automatiquement au premier accès

## 📧 Test en production

Une fois déployé, testez l'inscription :
1. Allez sur `https://votresite.alwaysdata.net`
2. Cliquez sur **Inscription**
3. Remplissez le formulaire
4. **Vous recevrez un email** avec le code de vérification
5. Saisissez le code pour finaliser l'inscription

## ✅ Checklist de déploiement

- [ ] Code pushé sur GitHub
- [ ] Site créé sur AlwaysData
- [ ] Dépôt Git lié
- [ ] Base de données accessible
- [ ] Premier test d'inscription réussi
- [ ] Email de vérification reçu

## 📝 Notes importantes

- **En développement local** : Le code s'affiche automatiquement (pas besoin d'email)
- **En production AlwaysData** : Les emails sont envoyés automatiquement
- **Sécurité** : Les codes expirent après 10 minutes
- **Validation** : Le compte n'est créé qu'après vérification du code

## 🎯 Support

Pour toute question sur le déploiement :
1. Consultez la documentation AlwaysData
2. Vérifiez les logs du serveur
3. Testez d'abord en local avec `localhost`

