# SAE_CyberCigales_G5

## Contributeurs
- BADJOUDJ Hana
- CONTRUCCI Lou
- DIAZ Gwenn
- GUZELBABA Imran
- SIMON Louis

## Projet
Projet universitaire réalisé en groupe dans le cadre de la SAÉ **CyberCigales** à l’Université d’Aix-Marseille.

L’objectif du projet est de développer une **application web PHP en architecture MVC** proposant un **escape game pédagogique** autour de la **cybersécurité** et de la **cryptographie**, en collaboration avec l’association **Cyber Cigales de Marseille**.
Le projet vise principalement à sensibiliser un public non spécialiste, notamment des lycéennes, à travers une approche ludique, interactive et accessible.

---

## Commandes Git utiles
```bash
git stash
git pull
git stash pop
```

---

## Présentation
L’application développée a pour objectif de proposer un support pédagogique interactif permettant :
- la sensibilisation aux enjeux de la cybersécurité ;
- l’apprentissage de notions simples de cryptographie ;
- l’identification de certaines menaces numériques ;
- une progression par énigmes dans un format escape game ;
- une expérience immersive et accessible.

Le choix d’un escape game numérique permet de favoriser l’engagement des utilisatrices et l’apprentissage par la pratique.

---

## Public cible
Le public principal visé est celui des **lycéennes** participant aux actions de l’association Cyber Cigales.
Un second type d’utilisateur est constitué des **administrateurs**, chargés de la gestion des comptes, du suivi de l’activité et de la progression dans le jeu.

---

## Architecture technique
Le projet repose sur une architecture **MVC (Model - View - Controller)** :
- **Models** : gestion des données et accès à la base ;
- **Views** : affichage des pages ;
- **Controllers** : gestion des interactions utilisateur et de la logique applicative.

Cette organisation permet une séparation claire des responsabilités, facilite la maintenance du code et rend le travail collaboratif plus efficace.

Le projet a également été géré avec **GitHub** en utilisant une stratégie de **feature branches**, afin d’assurer une bonne traçabilité de l’historique du code, d’éviter les régressions et de permettre un développement en parallèle des fonctionnalités.

---

## Technologies utilisées
- **PHP** pour la logique serveur ;
- **HTML / CSS / JavaScript** pour l’interface ;
- **Base de données relationnelle** pour la gestion des comptes et de la progression ;
- **PDO** pour un accès sécurisé à la base de données ;
- **Git / GitHub** pour la gestion de version ;
- **PHPUnit** pour les tests automatisés ;
- **PHPDoc** et outils de vérification de code pour améliorer la qualité et la documentation interne.

---

## Fonctionnalités principales

### Gestion des comptes
- inscription utilisateur ;
- connexion / déconnexion ;
- gestion des sessions ;
- mot de passe oublié ;
- suppression de compte ;
- validation de compte ;
- enregistrement des informations utilisateur en base de données.

### Gestion du jeu
- accès à l’escape game après authentification ;
- sélection d’une équipe (**Alice** ou **Bob**) ;
- accès progressif aux épreuves ;
- validation des réponses avec retour immédiat ;
- système de progression ;
- enregistrement de l’avancement ;
- gestion des indices ;
- coopération entre équipes pour la phase finale.

### Administration
L’interface administrateur permet :
- la consultation des comptes utilisateurs ;
- la gestion de la progression des joueuses ;
- le bannissement et le débannissement ;
- la validation manuelle des inscriptions ;
- la suppression d’un joueur ;
- la modification de l’adresse mail des joueurs ;
- l’accès à l’ensemble des épreuves et au suivi global de l’application.

---

## Parcours utilisateur
Le parcours utilisateur principal est le suivant :
1. Accès au site ;
2. Création d’un compte ou connexion ;
3. Sélection d’une équipe ;
4. Accès à l’escape game ;
5. Participation aux épreuves ;
6. Progression jusqu’à la résolution finale.

Chaque épreuve se compose généralement :
- d’un contenu narratif ;
- d’une énigme ;
- d’un champ de saisie ;
- d’un mécanisme de validation.

La progression dans le jeu dépend de la réussite des différentes étapes.

---

## Épreuves de l’escape game
La version finale du projet intègre plusieurs épreuves de sensibilisation à la cybersécurité et à la cryptographie :
1. Déchiffrement d’un message ;
2. Message caché ;
3. Labyrinthe ;
4. Identification d’un email de phishing ;
5. Création d’un mot de passe sécurisé ;
6. Analyse des indices ;
7. Recherche sur Instagram ;
8. Mise en commun entre équipes ;
9. Ouverture du coffre final.

Ces épreuves sont reliées par un scénario progressif destiné à rendre l’expérience plus immersive, interactive et collaborative.

---

## Sécurité
La sécurité constitue un axe important du projet. Plusieurs mécanismes ont été pris en compte :
- hachage des mots de passe ;
- vérification sécurisée des identifiants ;
- utilisation de **requêtes préparées** avec **PDO** contre les injections SQL ;
- séparation des rôles utilisateurs / administrateurs ;
- restriction d’accès aux pages sensibles ;
- gestion sécurisée des sessions ;
- protections supplémentaires autour des comptes et de l’authentification ;
- amélioration progressive de la sécurité au fil des releases.

---

## Tests et qualité du code
Le projet a fait l’objet d’un travail de qualité logicielle comprenant :
- documentation interne du code ;
- structuration claire des modules ;
- refactorisation pour un meilleur respect de l’architecture MVC ;
- vérification du bon fonctionnement général du site ;
- tests des épreuves et de la progression ;
- utilisation de **PHPUnit** pour les tests automatisés.

Les phases de test ont permis d’identifier puis de corriger différents problèmes afin d’améliorer la fiabilité globale du jeu et d’ajuster certaines énigmes au public cible.

---

## Organisation du projet
Le projet s’est développé en deux grandes phases :

### Release 1
Première version fonctionnelle de l’application comprenant :
- les fondations techniques du projet ;
- l’architecture générale du site ;
- la gestion des comptes utilisateurs ;
- la connexion et les sessions ;
- l’interface de base ;
- les premières pages liées au jeu ;
- les premières épreuves jouables ;
- une interface administrateur initiale.

### Release 2
Version plus complète du projet comprenant :
- l’implémentation complète de l’escape game ;
- l’intégration du scénario ;
- l’ajout de nouvelles épreuves ;
- l’amélioration de l’interface utilisateur ;
- la correction de bugs ;
- l’optimisation de la navigation ;
- l’amélioration de la progression et des indices ;
- plusieurs ajustements techniques et ergonomiques.

---

## Utilisation
L’entrée principale du projet se fait via :

```bash
index.php
```

Les pages principales sont accessibles depuis la racine ou via des routes paramétrées par les contrôleurs.
Les fonctionnalités liées au jeu nécessitent une authentification.
Les administrateurs disposent d’accès spécifiques pour gérer les comptes et la progression.

---

## Points à améliorer
Même si le projet est fonctionnel, plusieurs pistes d’amélioration restent possibles :
- documentation d’installation plus détaillée ;
- amélioration continue de la responsivité et de l’accessibilité ;
- poursuite de la refactorisation de certains contrôleurs ;
- amélioration de l’ergonomie de certaines interfaces ;
- enrichissement du contenu pédagogique des épreuves ;
- renforcement supplémentaire de la sécurité et de la journalisation ;
- ajout de commentaires et de documentation technique plus détaillés.

---

## Licence
Projet privé – réservé à un usage universitaire dans le cadre de la SAÉ CyberCigales.
