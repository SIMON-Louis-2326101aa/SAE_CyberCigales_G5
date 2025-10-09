SAE_CyberCigales_G5

Contributeurs
Louis SIMON
CONTRUCCI Lou
Imran Guzelbaba
Gwenn Diaz
Hana Badjoudj

Commandes a gardé :
git stash
git pull
git stash pop

Présentation
Projet universitaire réalisé en groupe pour la SAE « CyberCigales » à l'Université d'Aix-Marseille. Ce projet vise à développer une application web en PHP organisée autour du modèle MVC (Model-View-Controller), permettant la gestion sécurisée des utilisateurs et l'accès à diverses fonctionnalités liées à la cybersécurité.

Fonctionnalités clés
Authentification et gestion des sessions utilisateurs

Interface modulaire : pages d’accueil, mentions légales, formulaires

Gestion des accès et sécurité renforcée (.htaccess, autoloader, logs)

CRUD utilisateurs (à finaliser)

Responsivité et accessibilité (à améliorer)

Architecture et Organisation
text
SAE_CyberCigales_G5/
│
├── Modules/
│   ├── controller/
│   ├── model/
│   └── view/
│
├── includes/
│   ├── autoloader.php
│   ├── connexionDB.php
│   ├── constant.php
│   ├── controllerHandler.php
│   ├── logSecurity.php
│   └── viewHandler.php
│
├── public/
│   ├── assets/
│   │   ├── images/
│   │   ├── js/
│   │   └── styles/
│   └── 404.php
│
├── index.php
├── README.md
├── .gitignore
└── .htaccess
Utilisation
Accéder à la page d’accueil via index.php.

Les contrôleurs gèrent les modules utilisateurs.

Les pages principales sont accessibles depuis la racine ou via des routes paramétrées par le contrôleur.

Points à améliorer
Documentation détaillée (code, modules, installation avancée)

CRUD utilisateurs : finalisation et tests

Sécurité : renforcer les accès (anti-injection SQL, validation email, logs, rate limiter)

Responsivité du site pour tous les supports

Commentaires et documentation interne du code

Licence
Projet privé – réservé à l’usage universitaire pour la SAE.
