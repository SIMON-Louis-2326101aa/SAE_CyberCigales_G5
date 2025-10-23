# 📋 Instructions Rapides - Branche Rate Limiting

## 🎯 Cette branche ajoute :
- Blocage après 5 tentatives de connexion échouées (15 min)
- Messages informatifs pour l'utilisateur

---

## 🚀 Installation Rapide

### 1️⃣ Récupérer la branche
```bash
git checkout feature/connection-rate-limiting
git pull origin feature/connection-rate-limiting
```

### 2️⃣ Créer la table dans ta base de données
**Dans phpMyAdmin ou MySQL :**
Exécute le contenu du fichier `database_setup.sql`

**OU en ligne de commande :**
```bash
php setup_database.php
```

### 3️⃣ Configurer la connexion
**Créer le fichier** `config/database.env` :
```env
DB_HOST=localhost
DB_NAME=cybercigales_db
DB_USER=root
DB_PASS=ton_mot_de_passe
```

### 4️⃣ Installer et démarrer
```bash
composer install
php -S localhost:8000 -t public
```

---

## ✅ Test Rapide

1. Va sur la page de connexion
2. Entre **5 fois** un mauvais mot de passe
3. **Résultat attendu** : Message "Trop de tentatives... bloqué pendant X minute(s)"

---

## 📁 Fichiers Modifiés
- `Modules/controller/userController.php` - Logique de blocage
- `Modules/view/formConnectionView.php` - Messages d'erreur
- `Modules/model/loginAttemptModel.php` - Nouveau modèle

---

Des questions ? Contacte-moi ! 🎉
