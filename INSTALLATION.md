# 📦 Installation du Projet

## 🚀 Démarrage Rapide

### 1️⃣ Cloner le Projet

```bash
git clone https://github.com/SIMON-Louis-2326101aa/SAE_CyberCigales_G5.git
cd SAE_CyberCigales_G5
```

### 2️⃣ Installer les Dépendances PHP

Le dossier `vendor` n'est pas inclus dans Git. Il faut l'installer avec Composer :

```bash
composer install
```

> **Note** : Si tu n'as pas Composer, télécharge-le sur [getcomposer.org](https://getcomposer.org/)

### 3️⃣ Configurer la Base de Données

1. **Démarrer MySQL** (XAMPP, WAMP ou MySQL standalone)

2. **Créer la base de données** :
```sql
CREATE DATABASE IF NOT EXISTS cybercigales_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. **Configurer les accès** dans `config/database.env` :
```env
DB_HOST=localhost
DB_NAME=cybercigales_db
DB_USER=root
DB_PASS=ton_mot_de_passe
```

### 4️⃣ Créer les Tables

Exécute le script SQL via phpMyAdmin ou en ligne de commande :

```bash
mysql -u root -p cybercigales_db < database_team_setup.sql
```

### 5️⃣ Démarrer le Serveur

```bash
php -S localhost:8000 -t public
```

Puis ouvre : `http://localhost:8000`

---

## 📁 Structure après Installation

```
SAE_CyberCigales_G5/
├── vendor/              ← Créé par composer install (NON commité)
├── composer.json        ← Liste des dépendances (commité)
├── composer.lock        ← Versions exactes (NON commité)
├── config/
│   └── database.env     ← Configuration BDD (NON commité)
├── Modules/             ← Code source
├── public/              ← Point d'entrée web
└── ...
```

---

## ❓ FAQ

### Q : Pourquoi `vendor/` n'est pas dans Git ?
**R :** C'est trop lourd (plusieurs Mo). Chacun le génère avec `composer install`.

### Q : Pourquoi `composer.lock` n'est pas dans Git ?
**R :** Il peut être différent selon l'OS. `composer.json` suffit.

### Q : J'ai une erreur "Class not found" ?
**R :** Lance `composer install` pour générer l'autoloader.

### Q : Erreur de connexion à la base de données ?
**R :** Vérifie que MySQL est démarré et que `config/database.env` est correct.

---

## 🔧 Commandes Utiles

```bash
# Installer les dépendances
composer install

# Mettre à jour les dépendances
composer update

# Démarrer le serveur
php -S localhost:8000 -t public

# Vérifier les branches
git branch -a

# Basculer sur une branche
git checkout nom-de-la-branche
```

---

**Bon développement ! 🚀**

