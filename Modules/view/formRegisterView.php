<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <title>Inscription</title>
    <link rel="icon" href="/public/assets/images/favicon.ico">
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
</head>

<nav>
    <a href="index.php?controller=homepage&action=openHomepage" class="active">Home</a>
</nav>

<body>
    <h1>Inscription</h1>

    <?php if (isset($error)): ?>
        <div style="background-color: #ffebee; border: 1px solid #f44336; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p style="color: #c62828; font-weight: bold; margin: 0;">⚠️ <?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form action="index.php?controller=formRegister&action=register" method="post">

                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required><br>

                <label for="prenom">Prenom :</label>
                <input type="text" id="prenom" name="prenom" required><br>

                <label for="mail">E-mail :</label>
                <input type="email" id="mail" name="email" required><br>

                <label for="password">Mot de passe :</label>
                <input type="password" id="pwd" name="pwd" required><br>

                <label for="confirm_mdp">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_pwd" name="confirm_pwd" required><br>


        <button type="submit" name="register">S'inscrire</button>
    </form>
    <a href="index.php?controller=formConnection&action=login" class="active">Dejà un compte ? Connection</a>

<footer>
    <a href="index.php?controller=legalMention&action=legal" class="active">Mentions Légales</a>
</footer>

</body>
</html>