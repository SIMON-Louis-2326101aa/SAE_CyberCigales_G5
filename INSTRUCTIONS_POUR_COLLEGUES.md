# 📧 Instructions pour les Collègues - Branche Team Selection

Salut l'équipe ! 👋

J'ai créé une nouvelle branche qui implémente le **système de sélection d'équipe et le mode solo** pour notre projet CyberCigales.

---

## 🎯 Qu'est-ce que j'ai fait ?

J'ai ajouté un système complet qui permet aux joueurs de :
1. **Choisir leur mode de jeu** : Groupe (pour l'événement) ou Solo (pour tester)
2. **Sélectionner leur équipe** : Alice ou Bob
3. **Suivre leur progression** dans le jeu
4. **Enregistrer les codes** obtenus à chaque acte

C'est exactement ce dont on avait parlé pour gérer les 60 lycéennes le 18 mars !

---

## 📦 Nom de la Branche

```bash
feature/team-selection-solo-mode
```

---

## 🚀 Comment Tester Localement

### Étape 1 : Récupérer la Branche

```bash
# Se placer dans le dossier du projet
cd SAE_CyberCigales_G5

# Récupérer toutes les branches
git fetch

# Basculer sur la branche
git checkout feature/team-selection-solo-mode
```

### Étape 2 : Créer les Tables en Base de Données

**Option A : Via MySQL en ligne de commande**

```bash
mysql -u root -p votre_database < database_team_setup.sql
```

Remplacez `votre_database` par le nom de votre base de données.

**Option B : Via phpMyAdmin**

1. Ouvrir phpMyAdmin
2. Sélectionner votre base de données (ex: `cybercigales_db`)
3. Cliquer sur l'onglet **"SQL"**
4. Ouvrir le fichier `database_team_setup.sql` et copier tout son contenu
5. Coller dans la zone de texte
6. Cliquer sur **"Exécuter"**

### Étape 3 : Démarrer le Serveur

```bash
php -S localhost:8000 -t public
```

### Étape 4 : Tester les Fonctionnalités

1. **Sélection du Mode**
   - URL : `http://localhost:8000/index.php?controller=team&action=showModeSelection`
   - Tester les deux boutons (Groupe et Solo)

2. **Sélection d'Équipe (Mode Groupe)**
   - Choisir "Alice" ou "Bob"
   - Sélectionner une session (1, 2, 3 ou 4)
   - Cliquer sur "Rejoindre l'équipe"

3. **Sélection d'Équipe (Mode Solo)**
   - URL : `http://localhost:8000/index.php?controller=team&action=showTeamSelection&mode=solo`
   - Choisir "Alice" ou "Bob"
   - Cliquer sur "Commencer l'aventure"

4. **Informations de l'Équipe**
   - URL : `http://localhost:8000/index.php?controller=team&action=showTeamInfo`
   - Vérifier les infos affichées, les membres, les stats

---

## 📁 Fichiers Ajoutés (9 Fichiers)

### Base de Données
- `database_team_setup.sql` → Script SQL à exécuter

### Modèle
- `Modules/model/teamModel.php` → Gestion des équipes et progression

### Contrôleur
- `Modules/controller/teamController.php` → Logique de sélection et gestion

### Vues
- `Modules/view/teamModeSelectionView.php` → Page de choix du mode
- `Modules/view/teamSelectionView.php` → Page de sélection d'équipe
- `Modules/view/teamInfoView.php` → Page d'infos de l'équipe

### Documentation
- `CLARIFICATION_EQUIPES_ET_MODE_SOLO.md` → Explications conceptuelles
- `TEAM_SYSTEM_README.md` → Documentation technique complète
- `TEAM_BRANCH_SUMMARY.md` → Résumé de la branche

---

## 📊 Tables Créées en Base de Données

| Table | Description |
|-------|-------------|
| `teams` | Stocke les équipes (Alice/Bob, session, mode) |
| `team_members` | Associe les utilisateurs aux équipes |
| `team_progress` | Enregistre la progression dans les énigmes |
| `team_codes` | Stocke les fragments de code obtenus |
| `game_sessions` | Sessions de jeu planifiées (optionnel) |

---

## 🧪 Tests à Faire

### ✅ Test 1 : Créer une Équipe en Mode Groupe
1. Aller sur la page de sélection du mode
2. Cliquer sur "Mode Groupe"
3. Choisir "Alice" et "Session 1"
4. Vérifier que l'équipe est créée dans la table `teams`

### ✅ Test 2 : Créer une Équipe en Mode Solo
1. Aller sur la page de sélection du mode
2. Cliquer sur "Mode Solo"
3. Choisir "Bob"
4. Vérifier que `is_solo = 1` dans la table `teams`

### ✅ Test 3 : Rejoindre une Équipe Existante
1. Créer une équipe "Alice - Session 1" avec un premier utilisateur
2. Se connecter avec un deuxième utilisateur
3. Choisir "Alice - Session 1"
4. Vérifier que les deux utilisateurs sont dans la même équipe

### ✅ Test 4 : Quitter une Équipe
1. Rejoindre une équipe
2. Aller sur "Informations de l'Équipe"
3. Cliquer sur "Quitter l'équipe"
4. Vérifier que l'entrée est supprimée dans `team_members`

---

## 💡 Ce qu'il Reste à Faire

### Pour Intégration Complète

1. **Créer le contrôleur de jeu** (`gameController.php`)
   - Actions : `startActe()`, `showEnigme()`, `validateCode()`

2. **Créer les vues des énigmes**
   - Acte I : 10 pages d'énigmes (Cryptographie)
   - Acte II : 10 pages d'énigmes (Cybersécurité)
   - Acte III : 6 pages d'énigmes (Fusion)

3. **Intégrer avec teamController**
   - Appeler `saveProgress()` à chaque énigme résolue
   - Appeler `saveTeamCode()` à la fin des Actes I et II

4. **Implémenter la logique de fusion (Acte III)**
   - Récupérer les codes des deux équipes
   - En mode solo : fusionner automatiquement

---

## 🤝 Proposition de Répartition

Voici comment on pourrait se répartir le travail restant :

### 👤 Personne 1 : Game Controller
- Créer `Modules/controller/gameController.php`
- Actions : `startActe()`, `showEnigme()`, `validateCode()`

### 👤 Personne 2 : Énigmes Acte I (Cryptographie)
- Créer les 10 vues d'énigmes (César, Morse, Nombres premiers, etc.)
- Intégrer avec le gameController

### 👤 Personne 3 : Énigmes Acte II (Cybersécurité)
- Créer les 10 vues d'énigmes (Phishing, Password Game, Stéganographie, etc.)
- Intégrer avec le gameController

### 👤 Personne 4 : Énigmes Acte III (Fusion)
- Créer les 6 vues d'énigmes finales
- Implémenter la logique de fusion des codes

### 👤 Personne 5 : Design et CSS
- Harmoniser le design des pages d'énigmes
- Créer des animations et transitions
- Responsive design

---

## 📚 Documentation à Lire

1. **`CLARIFICATION_EQUIPES_ET_MODE_SOLO.md`**
   - Explique le concept du système
   - Répond aux questions "Pourquoi Alice/Bob ?", "Pourquoi 4 groupes ?", etc.

2. **`TEAM_SYSTEM_README.md`**
   - Documentation technique complète
   - Toutes les méthodes du modèle et contrôleur
   - Exemples d'utilisation

3. **`TEAM_BRANCH_SUMMARY.md`**
   - Résumé de ce qui a été fait
   - Architecture du système
   - Flux utilisateur

---

## ❓ Questions Fréquentes

### Q1 : Dois-je toucher au fichier `.env` ou `config/database.env` ?
**R :** Non, la configuration de la base de données existe déjà. Il suffit d'exécuter le script SQL.

### Q2 : Est-ce que ça va casser le code existant ?
**R :** Non, tous les nouveaux fichiers sont indépendants. Aucun fichier existant n'a été modifié.

### Q3 : Comment récupérer l'équipe actuelle dans mon code ?
**R :** Utilise le contrôleur :
```php
$teamController = new teamController();
$team = $teamController->getCurrentTeam();
// $team['team_id'], $team['team_name'], $team['is_solo']
```

### Q4 : Comment enregistrer la progression ?
**R :** Dans ton contrôleur de jeu :
```php
$teamController->saveProgress($_SESSION['team_id'], $acte, $pageNumber, $code);
```

### Q5 : Comment tester sans base de données ?
**R :** Il faut absolument créer les tables. C'est rapide (1 minute avec phpMyAdmin).

---

## 🐛 Problèmes Potentiels

### Problème : "Table 'teams' doesn't exist"
**Solution** : Tu n'as pas exécuté le script SQL. Retourne à l'Étape 2.

### Problème : "Undefined index: team_id"
**Solution** : L'utilisateur n'a pas rejoint d'équipe. Redirige-le vers la sélection :
```php
if (!isset($_SESSION['team_id'])) {
    header('Location: index.php?controller=team&action=showTeamSelection');
    exit();
}
```

### Problème : Les styles ne s'affichent pas
**Solution** : Vérifie que tu as bien le serveur lancé dans le dossier `public` :
```bash
php -S localhost:8000 -t public
```

---

## 📞 Contact

Si vous avez des questions ou si quelque chose ne fonctionne pas :
1. Regardez les 3 fichiers de documentation
2. Vérifiez que le script SQL a bien été exécuté
3. Regardez les logs PHP pour les erreurs
4. Contactez-moi !

---

## ✅ Checklist avant de Merge dans `main`

- [ ] Toutes les tables créées en BDD
- [ ] Tests effectués en local
- [ ] Pas d'erreurs dans les logs PHP
- [ ] Les 3 pages (mode, sélection, infos) fonctionnent
- [ ] Mode groupe et mode solo testés
- [ ] Documentation lue et comprise
- [ ] Prêt à intégrer avec le gameController

---

**Bonne chance pour la suite ! 🚀**

Si vous avez des idées d'amélioration ou des suggestions, n'hésitez pas à créer une nouvelle branche et à proposer un pull request.

---

**Créé le** : 24 octobre 2025  
**Branche** : `feature/team-selection-solo-mode`  
**Auteur** : Hana (avec l'aide de Claude 😊)

