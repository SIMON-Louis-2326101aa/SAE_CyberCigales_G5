<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <title>Inscription</title>
    <link rel="icon" href="/public/assets/images/favicon.ico">
</head>
<body>
    <h1>Inscription</h1>

    <form action="/register" method="post">
        <ul>
            <li>
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </li>
            <li>
                <label for="prenom">Prenom :</label>
                <input type="text" id="prenom" name="prenom" required>
            </li>
            <li>
                <label for="mail">E-mail :</label>
                <input type="email" id="mail" name="email" required>
            </li>
            <li>
                <label for="mdp">Mot de passe :</label><br>
                <input type="password" id="mdp" name="mdp" required>
            </li>
            <li>
                <label for="confirm_mdp">Confirmer le mot de passe :</label><br>
                <input type="password" id="confirm_mdp" name="confirm_mdp" required>
            </li>
        </ul>

        <button type="submit">S'inscrire</button>
    </form>
    <a href="index.php?controller=formConnection&action=login" class="active">Dejà un compte ? Connection</a>

<footer>
    <a href="index.php?controller=legalMention&action=login" class="active">Mentions Légales</a>
</footer>

</body>
</html>