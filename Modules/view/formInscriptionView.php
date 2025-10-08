<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <title>Inscription</title>
    <link rel="icon" href="/public/assets/images/favicon.ico">
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
</head>
<body>
    <h1>Inscription</h1>

    <form action="index.php?controller=formInscription&action=register" method="post">

                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required><br>

                <label for="prenom">Prenom :</label>
                <input type="text" id="prenom" name="prenom" required><br>

                <label for="mail">E-mail :</label>
                <input type="email" id="mail" name="email" required><br>

                <label for="password">Mot de passe :</label>
                <input type="password" id="mdp" name="mdp" required><br>

                <label for="confirm_mdp">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_mdp" name="confirm_mdp" required><br>


        <button type="submit" name="register">S'inscrire</button>
    </form>
    <a href="index.php?controller=formConnection&action=login" class="active">Dejà un compte ? Connection</a>

<footer>
    <a href="index.php?controller=legalMention&action=legal" class="active">Mentions Légales</a>
</footer>

</body>
</html>