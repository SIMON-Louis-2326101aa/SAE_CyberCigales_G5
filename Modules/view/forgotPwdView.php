<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title> Mot de passe oublié </title>
	<link rel="icon" href="/public/assets/images/favicon.ico">
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
</head>

<nav>
    <a href="index.php?controller=redirection&action=openHomepage" class="active">Home</a>
</nav>

<body>
<form action="index.php?controller=user&action=forgot" method="post">
	<h2>Vous avez oublié votre mot de passe</h2>
	<h3>Veuillez mettre votre Email pour réinitialiser votre mot de passe</h3>

        <label for="mail">E-mail :</label>
        <input type="email" id="mail" name="email" required><br>
        <button type="submit" name="forgotPwd">Réinitialiser le mot de passe</button>
    </form>
    <p><a href="index.php?controller=user&action=login" class="active">Retour</a></p>
</body>
</html>
