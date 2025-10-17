<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title> Connexion </title>
    <link rel="icon" href="/public/assets/images/favicon.ico">
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
</head>

<nav>
    <a href="index.php?controller=redirection&action=openHomepage" class="active">Home</a>
</nav>

<body>
<h1> Connection </h1>
<form action="index.php?controller=user&action=login"  method="post">
    E-mail : <input type="email" name="email" required> <br>
    Mot de passe : <input type="password" name="pwd" required><br>
    <button type="submit" name="login">Se connecter</button>
    <a href="index.php?controller=user&action=forgot" class="active">Mot de passe oublié?</a>
</form>
<a href="index.php?controller=user&action=register" class="active">Pas encore inscrit ?</a>
<footer>
    <a href="index.php?controller=redirection&action=openLegal" class="active">Mentions Légales</a>
</footer>
</body>
</html>
