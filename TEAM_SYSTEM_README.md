# 🎮 Système de Sélection d'Équipe et Mode Solo

## 📋 Description

Ce système permet aux joueurs de choisir entre deux modes de jeu :
- **Mode Groupe** : Pour jouer à plusieurs (4-6 joueurs par équipe)
- **Mode Solo** : Pour jouer seul(e)

Dans les deux modes, les joueurs choisissent de suivre **Alice** ou **Bob**, les deux personnages principaux de l'aventure "L'Héritage Chiffré".

---

## 🗂️ Fichiers Créés

### 1. **Base de Données**
- `database_team_setup.sql` : Script SQL pour créer les tables nécessaires

#### Tables créées :
- `teams` : Stocke les équipes créées (Alice ou Bob, session, mode)
- `team_members` : Association entre utilisateurs et équipes
- `team_progress` : Suivi de la progression des équipes dans les énigmes
- `team_codes` : Fragments de code obtenus par chaque équipe
- `game_sessions` : Sessions de jeu planifiées (optionnel)

### 2. **Modèle**
- `Modules/model/teamModel.php` : Gestion des équipes, membres, progression et codes

#### Méthodes principales :
- `createTeam()` : Créer une nouvelle équipe
- `addMemberToTeam()` : Ajouter un joueur à une équipe
- `getUserCurrentTeam()` : Obtenir l'équipe d'un utilisateur
- `saveProgress()` : Enregistrer la progression d'une équipe
- `saveTeamCode()` : Enregistrer un fragment de code
- `getTeamStats()` : Obtenir les statistiques d'une équipe

### 3. **Contrôleur**
- `Modules/controller/teamController.php` : Logique de sélection et gestion des équipes

#### Actions principales :
- `showModeSelection()` : Affiche la sélection du mode (groupe/solo)
- `showTeamSelection()` : Affiche la sélection d'équipe (Alice/Bob)
- `joinTeam()` : Créer ou rejoindre une équipe
- `showTeamInfo()` : Afficher les infos de l'équipe actuelle
- `leaveTeam()` : Quitter l'équipe

### 4. **Vues**
- `Modules/view/teamModeSelectionView.php` : Page de choix du mode
- `Modules/view/teamSelectionView.php` : Page de sélection d'équipe
- `Modules/view/teamInfoView.php` : Page d'informations de l'équipe

### 5. **Documentation**
- `CLARIFICATION_EQUIPES_ET_MODE_SOLO.md` : Explication conceptuelle du système
- `TEAM_SYSTEM_README.md` : Ce fichier (documentation technique)

---

## 🚀 Installation

### Étape 1 : Créer les tables dans la base de données

Exécutez le script SQL fourni :

```bash
# Via MySQL en ligne de commande
mysql -u votre_user -p votre_database < database_team_setup.sql

# Ou via phpMyAdmin :
# 1. Ouvrir phpMyAdmin
# 2. Sélectionner votre base de données
# 3. Aller dans l'onglet "SQL"
# 4. Copier/coller le contenu de database_team_setup.sql
# 5. Exécuter
```

### Étape 2 : Vérifier les fichiers

Assurez-vous que tous les fichiers sont bien présents :

```
SAE_CyberCigales_G5/
├── database_team_setup.sql
├── Modules/
│   ├── controller/
│   │   └── teamController.php
│   ├── model/
│   │   └── teamModel.php
│   └── view/
│       ├── teamModeSelectionView.php
│       ├── teamSelectionView.php
│       └── teamInfoView.php
└── TEAM_SYSTEM_README.md
```

### Étape 3 : Tester le système

1. Démarrer le serveur local :
```bash
php -S localhost:8000 -t public
```

2. Accéder aux pages :
- Sélection du mode : `http://localhost:8000/index.php?controller=team&action=showModeSelection`
- Sélection d'équipe : `http://localhost:8000/index.php?controller=team&action=showTeamSelection&mode=group`
- Mode solo : `http://localhost:8000/index.php?controller=team&action=showTeamSelection&mode=solo`

---

## 📖 Utilisation

### 1. Sélection du Mode de Jeu

Les joueurs arrivent sur une page avec deux options :

#### Mode Groupe 👥
- Permet de jouer à plusieurs (4-6 joueurs recommandés)
- Les joueurs choisissent une session (1, 2, 3 ou 4)
- Chaque session peut avoir plusieurs équipes Alice et Bob
- Idéal pour l'événement du 18 mars 2026

#### Mode Solo 🎯
- Permet de jouer seul(e)
- Pas de sélection de session
- Le joueur obtient automatiquement les deux moitiés du code à l'Acte III
- Idéal pour tester ou rejouer

### 2. Sélection de l'Équipe (Alice ou Bob)

Après avoir choisi le mode, les joueurs sélectionnent leur personnage :

#### Alice 👧
- Passionnée de cryptographie
- Découvre une lettre mystérieuse
- Obtient un fragment de code spécifique (ex: `CC------`)

#### Bob 👦
- Expert en cybersécurité en herbe
- Reçoit également une lettre codée
- Obtient un autre fragment de code (ex: `--CC----`)

### 3. Pendant le Jeu

Une fois l'équipe rejointe :
- L'ID de l'équipe est stocké dans `$_SESSION['team_id']`
- Le nom de l'équipe est dans `$_SESSION['team_name']`
- Le mode solo est dans `$_SESSION['is_solo']`

Ces informations permettent :
- De suivre la progression de l'équipe
- D'enregistrer les codes obtenus
- De synchroniser les actions entre les membres d'un groupe

---

## 🔧 Intégration avec le Jeu

### Enregistrer la Progression

Dans votre contrôleur de jeu, utilisez :

```php
$teamController = new teamController();

// Enregistrer qu'une énigme a été résolue
$teamController->saveProgress(
    $teamId,      // ID de l'équipe
    $acte,        // 1, 2 ou 3
    $pageNumber,  // Numéro de l'énigme
    $codeFound    // Code trouvé (optionnel)
);
```

### Enregistrer un Code d'Équipe

À la fin de chaque acte :

```php
// Acte I - Cryptographie
$teamController->saveTeamCode($teamId, 1, 'CC------'); // Pour Alice
$teamController->saveTeamCode($teamId, 1, '--CC----'); // Pour Bob

// Acte II - Cybersécurité
$teamController->saveTeamCode($teamId, 2, '----CC--'); // Pour Alice
$teamController->saveTeamCode($teamId, 2, '------CC'); // Pour Bob
```

### Récupérer les Codes pour l'Acte III

```php
// Récupérer le code de l'Acte I
$codeActe1 = $teamController->getTeamCode($teamId, 1);

// Récupérer le code de l'Acte II
$codeActe2 = $teamController->getTeamCode($teamId, 2);

// En mode solo, récupérer aussi les codes de "l'autre équipe"
if ($_SESSION['is_solo']) {
    // Logique pour fusionner automatiquement les codes
}
```

---

## 🎭 Scénarios d'Utilisation

### Scénario 1 : Événement du 18 Mars 2026

**14h00 - Session 1**
1. Groupe A (15 lycéennes) → Clique sur "Mode Groupe"
2. Choisit "Alice" et "Session 1"
3. Groupe B (15 lycéennes) → Clique sur "Mode Groupe"
4. Choisit "Bob" et "Session 1"
5. Les deux groupes jouent en parallèle

**15h00 - Session 2**
6. Groupe C → "Alice" + "Session 2"
7. Groupe D → "Bob" + "Session 2"

### Scénario 2 : Mode Solo (Test ou Rejeu)

1. Un enseignant veut tester le jeu
2. Clique sur "Mode Solo"
3. Choisit "Alice"
4. Joue les Actes I et II
5. À l'Acte III, obtient automatiquement les deux codes

### Scénario 3 : Changement d'Équipe

1. Un joueur rejoint "Alice" par erreur
2. Accède à "Informations de l'Équipe"
3. Clique sur "Quitter l'équipe"
4. Peut alors rejoindre "Bob"

---

## 📊 Structure de la Base de Données

### Table `teams`

| Champ | Type | Description |
|-------|------|-------------|
| team_id | INT | ID unique de l'équipe |
| team_name | VARCHAR(50) | "Alice" ou "Bob" |
| session_id | INT | Numéro de session (1, 2, 3, 4) |
| is_solo | BOOLEAN | Mode solo ou groupe |
| created_at | TIMESTAMP | Date de création |

### Table `team_members`

| Champ | Type | Description |
|-------|------|-------------|
| member_id | INT | ID unique |
| user_id | INT | Référence à users.id |
| team_id | INT | Référence à teams.team_id |
| joined_at | TIMESTAMP | Date d'ajout |

### Table `team_progress`

| Champ | Type | Description |
|-------|------|-------------|
| progress_id | INT | ID unique |
| team_id | INT | Référence à teams.team_id |
| acte | INT | 1 (Crypto), 2 (Cyber), 3 (Fusion) |
| page_number | INT | Numéro de l'énigme |
| code_found | VARCHAR(100) | Code découvert |
| completed | BOOLEAN | Énigme terminée ou non |
| completed_at | TIMESTAMP | Date de complétion |

### Table `team_codes`

| Champ | Type | Description |
|-------|------|-------------|
| code_id | INT | ID unique |
| team_id | INT | Référence à teams.team_id |
| acte | INT | 1 ou 2 |
| code_fragment | VARCHAR(20) | Fragment de code (ex: `CC------`) |
| obtained_at | TIMESTAMP | Date d'obtention |

---

## 🔐 Sécurité

### Vérifications Implémentées

1. **Authentification** : L'utilisateur doit être connecté pour accéder aux pages d'équipe
2. **Validation des entrées** : Les noms d'équipe sont validés (Alice ou Bob uniquement)
3. **Requêtes préparées** : Toutes les requêtes SQL utilisent des paramètres préparés
4. **Gestion des sessions** : Les informations d'équipe sont stockées en session

### Recommandations

- Ne jamais exposer les IDs d'équipe directement dans les URLs
- Valider toujours que l'utilisateur appartient à l'équipe avant d'afficher des données
- Limiter le nombre de changements d'équipe pour éviter les abus

---

## 🐛 Dépannage

### Problème : "Équipe non trouvée"

**Solution** : Vérifier que l'utilisateur est bien connecté et qu'il a rejoint une équipe.

```php
if (!isset($_SESSION['team_id'])) {
    // Rediriger vers la sélection d'équipe
    header('Location: index.php?controller=team&action=showTeamSelection');
}
```

### Problème : "Les codes ne s'enregistrent pas"

**Solution** : Vérifier que la méthode `saveTeamCode()` est appelée correctement :

```php
$success = $teamController->saveTeamCode($teamId, $acte, $codeFragment);
if (!$success) {
    error_log("Erreur lors de l'enregistrement du code");
}
```

### Problème : "Plusieurs utilisateurs dans la même session créent des équipes différentes"

**Solution** : C'est normal ! En mode groupe, plusieurs équipes "Alice" ou "Bob" peuvent exister pour la même session. Chaque équipe a un `team_id` unique.

---

## 📈 Améliorations Futures

### Fonctionnalités Possibles

1. **Dashboard Admin** : Voir toutes les équipes en temps réel
2. **Système de Hints** : Permettre aux organisateurs d'envoyer des indices à une équipe
3. **Classement** : Afficher un classement basé sur le temps de complétion
4. **Synchronisation en Temps Réel** : WebSocket pour voir la progression des autres équipes
5. **Statistiques Avancées** : Graphiques de progression, taux de réussite par énigme
6. **Export des Données** : Exporter les résultats en CSV pour analyse

---

## 📞 Support

Pour toute question ou problème :
1. Consulter ce README
2. Vérifier les logs d'erreur PHP
3. Contacter l'équipe de développement

---

**Date de création** : 24 octobre 2025  
**Auteur** : SAE CyberCigales G5  
**Version** : 1.0

