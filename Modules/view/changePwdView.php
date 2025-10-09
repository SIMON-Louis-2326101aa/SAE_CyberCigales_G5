<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changement mot de passe</title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
    <link rel="icon" href="/public/assets/images/favicon.ico">
</head>
<nav>
    <a href="index.php?controller=homepage&action=openHomepage" class="active">Home</a>
</nav>
<body>
<form method="POST" action="index.php?controller=changePwd&action=changePwd">
    <h2>Modifier mon mot de passe</h2>

    <label for="new_password">Nouveau mot de passe :</label>
    <input type="password" id="new_password" name="new_password" required><br>

    <label for="confirm_password">Confirmer mot de passe :</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br>

    <button type="submit" name="changePwd">Valider le changement</button>
</form>
<footer>
    <a href="index.php?controller=legalMention&action=legal" class="active">Mentions Légales</a>
</footer>
</body>
</html>

