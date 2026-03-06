<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$info = "<section >

    <h2>Pourquoi un mot de passe robuste est-il important ?</h2>

    <p>
        Un mot de passe est la première barrière de protection de vos comptes en ligne.
        Il permet de protéger vos informations personnelles, vos emails, vos photos,
        vos comptes bancaires ou encore vos réseaux sociaux.
    </p>

    <p>
        Si un mot de passe est trop simple ou trop court, il peut être facilement
        deviné ou trouvé par des programmes automatiques utilisés par des pirates
        informatiques. Une fois le mot de passe découvert, un attaquant peut accéder
        à vos comptes et voler vos données ou se faire passer pour vous.
    </p>

    <h2>Qu’est-ce qu’un mot de passe robuste ?</h2>

    <p>
        Un mot de passe robuste est un mot de passe difficile à deviner pour une
        personne ou un ordinateur. Il doit contenir plusieurs types de caractères
        et être suffisamment long.
    </p>

    <ul>
        <li>Au moins 12 caractères</li>
        <li>Des lettres minuscules</li>
        <li>Des lettres majuscules</li>
        <li>Des chiffres</li>
        <li>Des caractères spéciaux comme ! ? @ # % *</li>
    </ul>

    <h2>Les erreurs à éviter</h2>

    <p>Certains mots de passe sont très faciles à deviner et doivent être évités :</p>

    <ul>
        <li>Utiliser des mots simples comme password ou 123456</li>
        <li>Utiliser une date de naissance</li>
        <li>Utiliser son nom ou prénom</li>
        <li>Réutiliser le même mot de passe sur plusieurs sites</li>
    </ul>

    <h2>Comment créer un mot de passe sécurisé ?</h2>

    <p>
        Une méthode efficace consiste à créer une phrase facile à retenir mais
        difficile à deviner, puis à la transformer en mot de passe.
    </p>

    <p>
        Exemple de phrase : Mon chat noir adore dormir sur le canapé
    </p>

    <p>
        Exemple transformé : McN@d0slC!
    </p>

    <h2>Bonnes pratiques de sécurité</h2>

    <ul>
        <li>Utiliser un mot de passe différent pour chaque compte</li>
        <li>Activer la double authentification quand c'est possible</li>
        <li>Utiliser un gestionnaire de mots de passe</li>
        <li>Ne jamais partager son mot de passe</li>
        <li>Changer son mot de passe en cas de doute</li>
    </ul>

    <h2>À retenir</h2>

    <p>
        Un mot de passe long et complexe protège mieux vos comptes.
        Quelques minutes pour créer un bon mot de passe peuvent éviter
        de nombreux problèmes de sécurité.
    </p>

</section>";
$clue = "Vous n'aavez pas besoin d'indice pour cette epreuve, il suffit de suivre les règles pour créer 
un mot de passe sécurisé.";

require_once __DIR__ . "./../../includes/layout/headerClue.php";
?>
<div>
    <br>
    <p>Vous avez été victime de phishing, veuillez changer votre mot de passe.</p>
    <p>Votre mission est de créer un mot de passe sécurisé en suivant une série de règles qui apparaîtront au fur et
        à mesure.</p>
    <p>Atteignez toutes les règles pour valider votre mot de passe.</p>

    <div>
        <form id="password-game-form">
            <div>
                <label for="passwordInput">Votre mot de passe :</label>
                <input type="text" id="passwordInput" placeholder="Commencez à taper votre mot de passe ici...">
            </div>
            <div>
                <ul id="passwordRules">
                </ul>
            </div>
            <button type="submit" disabled>Valider le mot de passe</button>
        </form>
    </div>

    <div id="block-message-good-pwd">
        <p id="message-congratulation">Félicitations !</p>
        <p id="final-password-container">
            Votre mot de passe validé : <span id="final-password-display"></span>
        </p>
        <p>Votre mot de passe est excellent car il ne contient pas que des mots du dictionnaire, mais aussi de nombreux
            caractères variés (chiffres, majuscules, symboles) et aléatoires.</p>
        <p>Cette complexité le rend extrêmement difficile à craquer par des attaques de type "brute force" ou
            "dictionnaire".</p>
        <br>
        <a href="index.php?controller=Puzzle&action=validatePasswordGame" class="btn-nav" id="btn-after-pwdgame">
            Passer à l'épreuve suivante</a>
    </div>
</div>