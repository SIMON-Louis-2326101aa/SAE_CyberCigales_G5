# ⚡ Quick Start - Système de Sélection d'Équipe

## 🎯 En Bref

Cette branche ajoute un système complet pour que les joueurs puissent :
- Choisir entre **Mode Groupe** (événement) ou **Mode Solo** (test)
- Sélectionner leur personnage : **Alice** 👧 ou **Bob** 👦
- Suivre leur progression et enregistrer leurs codes

---

## 🚀 Installation Rapide (3 Étapes)

### 1️⃣ Récupérer la Branche

```bash
git checkout feature/team-selection-solo-mode
```

### 2️⃣ Créer les Tables

**Via phpMyAdmin** (le plus simple) :
1. Ouvrir phpMyAdmin
2. Sélectionner votre base de données
3. Onglet "SQL"
4. Copier/coller le contenu de `database_team_setup.sql`
5. Exécuter ✅

**Ou via terminal** :
```bash
mysql -u root -p votre_database < database_team_setup.sql
```

### 3️⃣ Tester

```bash
php -S localhost:8000 -t public
```

Puis aller sur : `http://localhost:8000/index.php?controller=team&action=showModeSelection`

---

## 📁 Fichiers Créés (10 Fichiers)

```
📦 SAE_CyberCigales_G5/
│
├── 🗄️ database_team_setup.sql              ← Script SQL (À EXÉCUTER EN PREMIER)
│
├── 📂 Modules/
│   ├── 📂 model/
│   │   └── teamModel.php                   ← Gestion des équipes (23 méthodes)
│   │
│   ├── 📂 controller/
│   │   └── teamController.php              ← Logique (13 actions)
│   │
│   └── 📂 view/
│       ├── teamModeSelectionView.php       ← Page 1 : Choix du mode
│       ├── teamSelectionView.php           ← Page 2 : Choix Alice/Bob
│       └── teamInfoView.php                ← Page 3 : Infos de l'équipe
│
└── 📂 Documentation/
    ├── CLARIFICATION_EQUIPES_ET_MODE_SOLO.md   ← Explications conceptuelles
    ├── TEAM_SYSTEM_README.md                   ← Doc technique complète
    ├── TEAM_BRANCH_SUMMARY.md                  ← Résumé de la branche
    ├── INSTRUCTIONS_POUR_COLLEGUES.md          ← Instructions d'installation
    └── QUICK_START.md                          ← Ce fichier
```

---

## 🎮 Flux Utilisateur

```
┌─────────────────────────────────────┐
│   1. Sélection du Mode de Jeu      │
│   (teamModeSelectionView)           │
│                                     │
│   [Mode Groupe 👥]  [Mode Solo 🎯]  │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│   2. Sélection de l'Équipe          │
│   (teamSelectionView)               │
│                                     │
│   [Alice 👧]        [Bob 👦]         │
│   Session: [1] [2] [3] [4]          │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│   3. Jouer l'Aventure               │
│   (Acte I → II → III)               │
│                                     │
│   Progression automatiquement       │
│   enregistrée en BDD                │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│   4. Informations de l'Équipe       │
│   (teamInfoView)                    │
│                                     │
│   - Membres                         │
│   - Progression                     │
│   - Codes obtenus                   │
└─────────────────────────────────────┘
```

---

## 📊 Tables en Base de Données

| Table | Description | Lignes Clés |
|-------|-------------|-------------|
| **teams** | Équipes créées | team_id, team_name (Alice/Bob), session_id, is_solo |
| **team_members** | Association users ↔ teams | user_id, team_id |
| **team_progress** | Progression dans les énigmes | team_id, acte, page_number, completed |
| **team_codes** | Fragments de code | team_id, acte, code_fragment |

---

## 🧪 Test Rapide

### Test 1 : Mode Groupe

1. Aller sur `http://localhost:8000/index.php?controller=team&action=showModeSelection`
2. Cliquer sur **"Mode Groupe"**
3. Choisir **"Alice"** et **"Session 1"**
4. Cliquer sur **"Rejoindre l'équipe"**
5. ✅ Vérifier la redirection et l'équipe créée

### Test 2 : Mode Solo

1. Aller sur `http://localhost:8000/index.php?controller=team&action=showModeSelection`
2. Cliquer sur **"Mode Solo"**
3. Choisir **"Bob"**
4. Cliquer sur **"Commencer l'aventure"**
5. ✅ Vérifier que `is_solo = 1` dans la table `teams`

### Test 3 : Infos d'Équipe

1. Après avoir rejoint une équipe
2. Aller sur `http://localhost:8000/index.php?controller=team&action=showTeamInfo`
3. ✅ Vérifier l'affichage des infos, membres, stats

---

## 💡 Utilisation dans Votre Code

### Récupérer l'Équipe Actuelle

```php
// Dans n'importe quel contrôleur
$teamController = new teamController();
$team = $teamController->getCurrentTeam();

// Utiliser les données
$teamId = $team['team_id'];
$teamName = $team['team_name'];  // "Alice" ou "Bob"
$isSolo = $team['is_solo'];      // true ou false
```

### Enregistrer la Progression

```php
// Quand un joueur résout une énigme
$teamController->saveProgress(
    $_SESSION['team_id'],  // ID de l'équipe
    1,                     // Acte (1, 2 ou 3)
    5,                     // Numéro de page/énigme
    'CESAR'                // Code trouvé (optionnel)
);
```

### Enregistrer un Code d'Équipe

```php
// À la fin de l'Acte I
$teamController->saveTeamCode(
    $_SESSION['team_id'],
    1,           // Acte 1
    'CC------'   // Fragment pour Alice
);
```

### Récupérer un Code

```php
// Pour l'Acte III (Fusion)
$codeActe1 = $teamController->getTeamCode($_SESSION['team_id'], 1);
$codeActe2 = $teamController->getTeamCode($_SESSION['team_id'], 2);

// Combiner les codes
$finalCode = $codeActe1 . $codeActe2;
```

---

## 🎨 Pages Web Disponibles

| URL | Description |
|-----|-------------|
| `?controller=team&action=showModeSelection` | Choix du mode (groupe/solo) |
| `?controller=team&action=showTeamSelection&mode=group` | Sélection d'équipe (groupe) |
| `?controller=team&action=showTeamSelection&mode=solo` | Sélection d'équipe (solo) |
| `?controller=team&action=showTeamInfo` | Informations de l'équipe |
| `?controller=team&action=leaveTeam` | Quitter l'équipe |

---

## 🔧 Méthodes du teamModel

### Gestion des Équipes
- `createTeam($teamName, $sessionId, $isSolo)` → Créer une équipe
- `getTeamByNameAndSession()` → Récupérer une équipe existante
- `getSessionTeams($sessionId)` → Toutes les équipes d'une session

### Gestion des Membres
- `addMemberToTeam($userId, $teamId)` → Ajouter un membre
- `getUserCurrentTeam($userId)` → Équipe actuelle d'un utilisateur
- `getTeamMembers($teamId)` → Liste des membres
- `countTeamMembers($teamId)` → Nombre de membres
- `removeMemberFromTeam()` → Retirer un membre

### Progression
- `saveProgress($teamId, $acte, $pageNumber, $code)` → Enregistrer une énigme
- `getTeamProgress($teamId, $acte)` → Récupérer la progression
- `getTeamStats($teamId)` → Statistiques complètes

### Codes
- `saveTeamCode($teamId, $acte, $codeFragment)` → Sauvegarder un code
- `getTeamCodes($teamId)` → Tous les codes d'une équipe
- `getTeamCodeForActe($teamId, $acte)` → Code d'un acte spécifique

---

## 📚 Documentation Complète

| Fichier | Contenu |
|---------|---------|
| `CLARIFICATION_EQUIPES_ET_MODE_SOLO.md` | ❓ Pourquoi Alice/Bob ? Pourquoi 4 groupes ? |
| `TEAM_SYSTEM_README.md` | 📖 Documentation technique complète (scénarios, BDD, méthodes) |
| `TEAM_BRANCH_SUMMARY.md` | 📝 Résumé de ce qui a été fait |
| `INSTRUCTIONS_POUR_COLLEGUES.md` | 👥 Guide d'installation pour l'équipe |
| `QUICK_START.md` | ⚡ Ce fichier (démarrage rapide) |

---

## ❓ FAQ Rapide

**Q : Dois-je modifier des fichiers existants ?**  
**R :** Non, tout est nouveau. Aucun fichier existant n'est touché.

**Q : Ça va casser mon code actuel ?**  
**R :** Non, c'est totalement indépendant.

**Q : Combien de temps pour installer ?**  
**R :** 5 minutes (récupérer branche + créer tables).

**Q : Comment tester sans BDD ?**  
**R :** Il faut absolument créer les tables (script SQL fourni).

**Q : Est-ce compatible avec le rate-limiting ?**  
**R :** Oui, totalement compatible avec toutes les autres branches.

---

## ✅ Checklist d'Installation

- [ ] Récupérer la branche : `git checkout feature/team-selection-solo-mode`
- [ ] Exécuter le script SQL : `database_team_setup.sql`
- [ ] Vérifier que les 5 tables sont créées en BDD
- [ ] Démarrer le serveur : `php -S localhost:8000 -t public`
- [ ] Tester la page de sélection du mode
- [ ] Tester la création d'une équipe (Alice ou Bob)
- [ ] Tester l'affichage des infos d'équipe

---

## 🚨 Problèmes Courants

### "Table 'teams' doesn't exist"
→ Tu n'as pas exécuté le script SQL. Retourne à l'Étape 2.

### "Undefined index: team_id"
→ L'utilisateur n'a pas rejoint d'équipe. Redirige vers la sélection :
```php
if (!isset($_SESSION['team_id'])) {
    header('Location: index.php?controller=team&action=showTeamSelection');
}
```

### Les styles ne s'affichent pas
→ Vérifie que le serveur est lancé avec `-t public` :
```bash
php -S localhost:8000 -t public
```

---

## 🎉 C'est Tout !

Tu es maintenant prêt(e) à utiliser le système de sélection d'équipe !

Pour toute question, consulte les fichiers de documentation ou contacte l'équipe.

**Bon développement ! 🚀**

---

**Créé le** : 24 octobre 2025  
**Branche** : `feature/team-selection-solo-mode`  
**Statut** : ✅ Prêt à utiliser

