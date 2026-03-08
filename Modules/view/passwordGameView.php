<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$info = "password";
$clue = "Vous n'avez pas besoin d'indice pour cette épreuve, il suffit de suivre les règles pour créer 
un mot de passe sécurisé.";

require_once __DIR__ . "/../../includes/layout/headerClue.php";
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