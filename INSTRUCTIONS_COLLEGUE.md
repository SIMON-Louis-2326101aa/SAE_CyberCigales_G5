# 📋 Instructions pour Configurer la Branche Rate Limiting

Bonjour ! Voici les instructions pour tester la nouvelle fonctionnalité de limitation des tentatives de connexion.

## 🎯 Objectif de cette Branche
Cette branche ajoute une **protection contre les tentatives de connexion multiples** :
- Maximum 5 tentatives par email → blocage de 15 minutes
- Maximum 10 tentatives par IP → blocage de 30 minutes
- Messages informatifs pour l'utilisateur

---

## 📦 Étape 1 : Récupérer le Code

```bash
# Récupérer les dernières modifications
git fetch origin

# Se placer sur la branche
git checkout feature/connection-rate-limiting

# S'assurer d'avoir la dernière version
git pull origin feature/connection-rate-limiting
```

---

## 🗄️ Étape 2 : Configurer la Base de Données

### A. Créer le fichier de configuration

1. **Créer le dossier config** s'il n'existe pas :
   ```bash
   mkdir config
   ```

2. **Créer le fichier** `config/database.env` avec ce contenu :
   ```env
   # Configuration de la base de données
   DB_HOST=localhost
   DB_NAME=cybercigales_db
   DB_USER=root
   DB_PASS=
   
   # Configuration pour le développement
   APP_ENV=development
   APP_DEBUG=true
   ```

3. **Modifier les valeurs** selon ta configuration MySQL :
   - `DB_HOST` : généralement `localhost` ou `127.0.0.1`
   - `DB_NAME` : nom de ta base de données
   - `DB_USER` : ton utilisateur MySQL (souvent `root`)
   - `DB_PASS` : ton mot de passe MySQL (laisser vide si pas de mot de passe)

### B. Créer la table login_attempts

**Option 1 : Utiliser le script automatique** (Recommandé)
```bash
php setup_database.php
```
✅ Ce script va automatiquement créer la table avec tous les index nécessaires.

**Option 2 : Utiliser phpMyAdmin**
1. Ouvre phpMyAdmin (http://localhost/phpmyadmin)
2. Sélectionne ta base de données `cybercigales_db`
3. Va dans l'onglet "SQL"
4. Copie et exécute le contenu du fichier `database_setup.sql`

**Option 3 : En ligne de commande MySQL**
```bash
mysql -u root -p cybercigales_db < database_setup.sql
```

---

## 🔧 Étape 3 : Installer les Dépendances

```bash
composer install
```

---

## 🚀 Étape 4 : Démarrer le Serveur

```bash
php -S localhost:8000 -t public
```

Puis ouvre ton navigateur sur : **http://localhost:8000**

---

## ✅ Étape 5 : Tester la Fonctionnalité

### Test 1 : Tentatives échouées
1. Va sur la page de connexion
2. Entre un email valide mais un **mauvais mot de passe**
3. Répète 5 fois
4. **Résultat attendu** : Au bout de 5 tentatives, tu verras un message de blocage temporaire

### Test 2 : Messages informatifs
- Après 1 tentative échouée : "Il vous reste 4 tentative(s)..."
- Après 2 tentatives : "Il vous reste 3 tentative(s)..."
- Après 5 tentatives : "Trop de tentatives... bloqué pendant X minute(s)"

### Test 3 : Connexion réussie
1. Attends le délai de blocage (ou réessaye avec un autre email)
2. Entre les **bons identifiants**
3. **Résultat attendu** : Connexion réussie + compteur remis à zéro

---

## 📁 Fichiers Modifiés/Créés

### Nouveaux fichiers :
- ✅ `Modules/model/loginAttemptModel.php` - Gestion des tentatives
- ✅ `database_setup.sql` - Script SQL de création de table
- ✅ `setup_database.php` - Script automatique de configuration
- ✅ `includes/cleanupLoginAttempts.php` - Nettoyage automatique
- ✅ `RATE_LIMITING_README.md` - Documentation complète

### Fichiers modifiés :
- ✅ `Modules/controller/userController.php` - Logique de rate limiting
- ✅ `Modules/view/formConnectionView.php` - Affichage des messages
- ✅ `public/index.php` - Chargement du fichier de configuration

---

## 🐛 Problèmes Courants

### Erreur : "Connexion à la base de données refusée"
**Solution** : Vérifie que MySQL est démarré (XAMPP/WAMP/MAMP)

### Erreur : "Table login_attempts doesn't exist"
**Solution** : Exécute le script `php setup_database.php`

### Erreur : "vendor/autoload.php not found"
**Solution** : Lance `composer install`

### Erreur : "Variables d'environnement manquantes"
**Solution** : Crée le fichier `config/database.env` avec tes paramètres

---

## 📊 Structure de la Table login_attempts

```sql
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_time (email, attempted_at),
    INDEX idx_ip_time (ip_address, attempted_at)
);
```

---

## 📞 Besoin d'Aide ?

Si tu rencontres un problème :
1. Vérifie les logs du serveur PHP
2. Consulte `RATE_LIMITING_README.md` pour plus de détails
3. Contacte-moi !

---

## ✨ Bon Test !

Une fois que tout fonctionne, on pourra merger cette branche dans `main` ! 🎉
