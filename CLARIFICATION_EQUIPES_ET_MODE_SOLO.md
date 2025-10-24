# 🎮 Clarification : Sélection d'Équipe et Mode Solo

## 📋 Question de Départ

> "Ma collègue m'a dit que dans l'intrigue, il y a Alice et Bob (donc 2 équipes), mais dans la structure, on mentionne un système de sélection d'équipe (4 groupes) et un mode solo. Comment ces éléments s'articulent-ils ?"

---

## ✅ Explication Complète

### 🎭 **Alice et Bob = L'Intrigue Narrative (Immersion)**

Alice et Bob sont **des personnages fictifs** que les joueurs incarnent dans l'histoire :

- **Alice** : Un personnage du jeu que l'équipe 1 suit
- **Bob** : Un personnage du jeu que l'équipe 2 suit

**Pourquoi deux personnages ?**

Pour créer une **dynamique de coopération** entre les groupes :
- Chaque personnage a une partie du code secret
- Les deux groupes doivent échanger leurs découvertes pour terminer le jeu
- Cela renforce l'aspect collaboratif et pédagogique

---

### 👥 **4 Groupes = Organisation Pratique (Logistique)**

Le **système de sélection d'équipe** avec 4 groupes est pour **gérer la session physique** du 18 mars 2026 :

#### Scénario prévu :
- **60 lycéennes** participent l'après-midi
- Divisées en **4 groupes de 15 personnes** (environ)
- Les groupes font des **rotations** avec des pauses

#### Comment ça fonctionne techniquement ?

**Option 1 : Deux groupes à la fois**
- Groupe 1 (15 personnes) → joue Alice
- Groupe 2 (15 personnes) → joue Bob
- **Pause** : Groupes 1 et 2 se reposent
- Groupe 3 (15 personnes) → joue Alice
- Groupe 4 (15 personnes) → joue Bob

**Option 2 : Duos qui choisissent leur camp**
- Les 4 groupes jouent en parallèle
- Chaque groupe choisit : "Équipe Alice" ou "Équipe Bob"
- Plusieurs groupes peuvent jouer Alice en même temps (avec des codes différents)

---

### 🎯 **Mode Solo = Accessibilité et Démo**

Le **mode solo** permet de :

#### 1. **Tester le jeu avant l'événement**
- Les enseignants ou organisateurs peuvent faire une démo
- Vérifier que tout fonctionne

#### 2. **Permettre un accès individuel**
- Une lycéenne curieuse peut rejouer chez elle
- Exploration personnelle après l'atelier

#### 3. **Adapter la difficulté**
- Jouer à son rythme
- Ne pas dépendre d'un groupe

#### Comment ça marche en mode solo ?
- Le joueur choisit **Alice OU Bob** au départ
- Il résout **les deux parcours successivement** (Acte I et II)
- À l'Acte III (fusion), il accède aux deux moitiés du code automatiquement

---

## 🧩 Récapitulatif : Comment Tout S'Articule

| Élément | Rôle | Contexte |
|---------|------|----------|
| **Alice & Bob** | Personnages de l'intrigue | Immersion narrative |
| **4 Groupes** | Organisation logistique | Gestion de la session du 18 mars |
| **Mode Solo** | Accessibilité | Test, démo, rejouabilité individuelle |

---

## 🎬 Exemple Concret de Déroulement

### Jour de l'Événement (18 mars 2026)

**14h00 - Accueil et introduction**
- Présentation du projet
- Explication des règles

**14h15 - Session 1**
- **Groupe A** (15 lycéennes) → Se connecte, choisit "Équipe Alice"
- **Groupe B** (15 lycéennes) → Se connecte, choisit "Équipe Bob"
- Ils jouent pendant 45 minutes

**15h00 - Pause + Rotation**
- Groupes A et B → Pause goûter
- **Groupe C** → Équipe Alice
- **Groupe D** → Équipe Bob

**15h45 - Debriefing**
- Explication des concepts de cybersécurité et cryptographie
- Retour d'expérience

---

### En Mode Solo (après l'événement)

**Exemple : Emma veut rejouer chez elle**

1. Elle se connecte au site
2. Clique sur **"Jouer en solo"**
3. Choisit **"Alice"** (ou Bob)
4. Résout les énigmes de l'Acte I (Cryptographie)
5. Résout les énigmes de l'Acte II (Cybersécurité)
6. À l'Acte III, elle obtient **automatiquement les deux moitiés du code** (car elle est seule)
7. Elle termine le jeu avec la révélation finale

---

## 💡 Pourquoi Cette Structure ?

### ✅ Avantages de cette organisation :

1. **Flexibilité logistique** : Gérer 60 personnes en groupe
2. **Coopération renforcée** : Alice et Bob doivent collaborer
3. **Accessibilité** : Mode solo pour tester ou rejouer
4. **Immersion narrative** : Les personnages donnent du sens à l'histoire
5. **Rejouabilité** : Chaque groupe peut avoir des énigmes légèrement différentes

---

## 🔧 Implémentation Technique (Résumé)

### Base de Données
```sql
CREATE TABLE teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(50), -- "Alice" ou "Bob"
    session_id INT, -- Pour gérer les 4 groupes (1, 2, 3, 4)
    is_solo BOOLEAN DEFAULT FALSE
);

CREATE TABLE players (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    player_name VARCHAR(100),
    team_id INT,
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
);
```

### Page de Sélection
```php
// Choix du mode
if ($_POST['mode'] == 'group') {
    // Afficher : "Choisissez votre équipe : Alice ou Bob"
    // Afficher : "Numéro de session : 1, 2, 3, ou 4"
} elseif ($_POST['mode'] == 'solo') {
    // Afficher : "Choisissez votre personnage : Alice ou Bob"
    // is_solo = TRUE
}
```

---

## 🎯 Conclusion

**Alice et Bob** = **personnages de l'histoire** (immersion)  
**4 groupes** = **gestion pratique** de l'événement  
**Mode solo** = **accessibilité** et test

Ces trois éléments sont **complémentaires** et permettent de créer une expérience :
- 📖 **Narrative** (Alice & Bob)
- 👥 **Collaborative** (groupes qui coopèrent)
- 🎮 **Accessible** (mode solo)

---

## 📞 Questions Fréquentes

**Q : Si on a 4 groupes, pourquoi seulement 2 personnages ?**  
R : Les 4 groupes se répartissent entre Alice et Bob (2 groupes jouent Alice, 2 jouent Bob), ou jouent en rotation.

**Q : En mode solo, comment fonctionne la fusion ?**  
R : Le joueur accède automatiquement aux deux moitiés du code, car il n'y a pas d'autre équipe.

**Q : Peut-on avoir plus de 2 personnages ?**  
R : Oui, mais cela compliquerait la narration. Alice et Bob suffisent pour créer une dynamique de coopération.

---

**Date de rédaction** : 24 octobre 2025  
**Auteur** : Assistant IA - Projet SAE CyberCigales G5

