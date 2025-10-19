# 📋 Messages utilisateur - Système de vérification email

## 🎯 Messages de succès

### ✅ Inscription réussie (Page de connexion)
**Quand** : Après validation du code de vérification et création du compte
**Message** :
```
🎉 Inscription réussie ! Votre compte a été créé et votre email a été vérifié. 
Vous pouvez maintenant vous connecter.
```
**Couleur** : Vert (succès)

### ℹ️ Code envoyé (Page de vérification)
**Quand** : Après demande d'un code de vérification
**Message** :
```
ℹ️ Un code vous a été envoyé.
```
**Couleur** : Bleu (information)

### 🔑 Code affiché en développement
**Quand** : En environnement local (localhost)
**Message** :
```
🔑 Code (dev/local): 123456
```
**Couleur** : Orange (attention développeur)

## ⚠️ Messages d'erreur - Inscription

### Email déjà vérifié
**Quand** : Tentative d'inscription avec un email existant
**Message** :
```
⚠️ Un compte avec cet email existe déjà. Connectez-vous ici ou utilisez un autre email.
```
**Action** : Lien vers la page de connexion
**Couleur** : Rouge

### Email en attente de vérification
**Quand** : Tentative d'inscription avec un email en cours de vérification
**Message** :
```
⚠️ Une inscription est déjà en cours pour cet email. Cliquez ici pour recevoir un nouveau code.
```
**Action** : Lien pour renvoyer le code
**Couleur** : Rouge

### Mots de passe différents
**Quand** : Les deux champs de mot de passe ne correspondent pas
**Message** :
```
⚠️ Les mots de passe ne correspondent pas.
```
**Couleur** : Rouge

## ❌ Messages d'erreur - Vérification email

### Code incorrect
**Quand** : Le code saisi n'existe pas dans la base de données
**Message** :
```
❌ Le code saisi est incorrect. Veuillez vérifier et réessayer.
```
**Couleur** : Rouge

### Code expiré
**Quand** : Le code existe mais a dépassé les 10 minutes de validité
**Message** :
```
❌ Ce code a expiré. Les codes sont valables 10 minutes. Demander un nouveau code.
```
**Action** : Lien pour recevoir un nouveau code
**Couleur** : Rouge

### Format invalide
**Quand** : Le code ne respecte pas le format (6 chiffres)
**Message** :
```
❌ Veuillez respecter le format: 6 chiffres.
```
**Couleur** : Rouge

### Erreur de création de compte
**Quand** : Problème technique lors de la création du compte après vérification
**Message** :
```
❌ Erreur lors de la création du compte. Veuillez réessayer.
```
**Couleur** : Rouge

## 🎨 Codes couleur

| Type | Couleur | Usage |
|------|---------|-------|
| Succès | Vert (`#4caf50`) | Opération réussie |
| Information | Bleu (`#2196f3`) | Information neutre |
| Avertissement | Orange (`#ff9800`) | Attention développeur |
| Erreur | Rouge (`#f44336`) | Erreur utilisateur |

## 🔗 Actions disponibles

1. **Lien vers connexion** : `index.php?controller=formConnection&action=login`
2. **Renvoyer un code** : `index.php?controller=emailVerification&action=request&email={email}`
3. **Page d'inscription** : `index.php?controller=formRegister&action=register`

## 📱 Responsive

Tous les messages sont affichés dans des boîtes :
- `padding: 15px`
- `border-radius: 5px`
- `margin: 15px 0`
- Police en gras pour la lisibilité
- Icônes emoji pour identification rapide

## ✅ Checklist UX

- [x] Messages clairs et explicites
- [x] Distinction entre erreurs (code incorrect vs expiré)
- [x] Liens d'action directement dans les messages
- [x] Codes couleur cohérents
- [x] Icônes pour identification visuelle rapide
- [x] Messages de succès encourageants
- [x] Guidance de l'utilisateur (liens vers actions suivantes)

