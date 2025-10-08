<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title> Connexion </title>
    <link rel="icon" href="/public/assets/images/favicon.ico">
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
</head>

<body>
<h1> Connection </h1>
<form action="index.php?controller=formConnection&action=login"  method="post">
    E-mail : <input type="email" name="email"> <br>
    Mot de passe : <input type="password" name="mdp"><br>
    <button type="submit" name="login">Se connecter</button>
    <a href="index.php?controller=formMdpOublie&action=mdpforgot" class="active">Mot de passe oublié?</a>
</form>
<a href="index.php?controller=formInscription&action=register" class="active">Pas encore inscrit ?</a>
<footer>
    <a href="index.php?controller=legalMention&action=legal" class="active">Mentions Légales</a>
</footer>
</body>
</html>
