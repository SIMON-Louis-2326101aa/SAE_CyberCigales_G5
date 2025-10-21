# 📋 Guide de migration - Table pending_registrations

## 🎯 Pour qui ?

Ce guide est pour **toute personne** qui récupère le projet depuis GitHub et doit mettre à jour sa base de données.

## ⚠️ Nouvelle table requise

Le système de vérification email a été amélioré et nécessite maintenant une nouvelle table : **`pending_registrations`**

## 🚀 Méthode 1 : Migration automatique (Recommandée)

La table se crée **automatiquement** lors de la première inscription. Il suffit de :

1. **Récupérer les dernières modifications** :
   ```bash
   git pull origin feature/email-verification-code
   ```

2. **Tester une inscription** sur le site :
   - La table sera créée automatiquement
   - Pas besoin d'exécuter de SQL manuellement

3. **Vérifier que la table existe** :
   - Se connecter à phpMyAdmin ou la base de données
   - Vérifier la présence de la table `pending_registrations`

## 🔧 Méthode 2 : Migration manuelle (Si besoin)

Si la table ne se crée pas automatiquement, exécuter le fichier SQL :

### Via phpMyAdmin (AlwaysData) :
1. Se connecter à phpMyAdmin
2. Sélectionner la base de données `escapethecode_bd`
3. Aller dans l'onglet **SQL**
4. Copier/coller le contenu de `migration_pending_registrations.sql`
5. Cliquer sur **Exécuter**

### Via ligne de commande :
```bash
mysql -h mysql-escapethecode.alwaysdata.net -u 433487 -p escapethecode_bd < migration_pending_registrations.sql
```

## 📊 Tables créées/modifiées

### 1. Nouvelle table : `pending_registrations`
Stocke les inscriptions en attente de vérification email :
- `id` : Identifiant unique
- `nom` : Nom de famille
- `prenom` : Prénom
- `email` : Email (indexé)
- `password` : Mot de passe hashé
- `created_at` : Date de création

### 2. Table modifiée : `users`
Ajout du champ :
- `email_verified` : BOOLEAN (TRUE si email vérifié)

## ✅ Vérification

Pour vérifier que la migration a fonctionné :

```sql
-- Vérifier l'existence de la table
SHOW TABLES LIKE 'pending_registrations';

-- Vérifier la structure
DESCRIBE pending_registrations;

-- Vérifier le nouveau champ dans users
DESCRIBE users;
```

## 🔄 Nouveau flux d'inscription

Avec cette table, le système fonctionne maintenant ainsi :

1. **Inscription** → Données dans `pending_registrations` (compte non créé)
2. **Code envoyé** → Email de vérification
3. **Code validé** → Compte créé dans `users` + suppression de `pending_registrations`
4. **Code incorrect** → Aucun compte créé (sécurité)

## 🐛 Problèmes courants

### La table n'apparaît pas ?
- Faire une inscription test sur le site
- Vérifier les droits SQL (CREATE TABLE)
- Exécuter manuellement le fichier `migration_pending_registrations.sql`

### Erreur "Table doesn't exist" ?
- Exécuter la migration SQL manuellement
- Vérifier la connexion à la base de données

## 📝 Notes importantes

- La table se vide automatiquement après chaque vérification réussie
- Les inscriptions non vérifiées restent dans cette table
- Prévoir un nettoyage périodique des inscriptions anciennes (optionnel)

## 💡 Pour les développeurs

Le code de création automatique se trouve dans :
- **Fichier** : `Modules/model/emailVerificationModel.php`
- **Méthode** : `ensureTable()` (lignes 12-35)
- **Appelé** : Au constructeur du modèle

## ✅ Checklist

- [ ] Git pull effectué
- [ ] Code récupéré
- [ ] Migration SQL exécutée (si nécessaire)
- [ ] Table `pending_registrations` visible dans la base
- [ ] Champ `email_verified` ajouté à la table `users`
- [ ] Test d'inscription effectué
- [ ] Système de vérification email fonctionnel

## 🆘 Besoin d'aide ?

Si problème, vérifier :
1. Les logs du serveur web
2. Les logs PHP
3. Les permissions de la base de données
4. La connexion à la base dans `includes/connectionDB.php`

