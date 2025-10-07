<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title> Connexion </title>
    <link rel="icon" href="/public/assets/images/favicon.ico">
    <link rel="stylesheet" href="./_assets/styles/stylesheet.css">
</head>

<body>
<h1> Connection </h1>
<form action="/login"  method="post">
    E-mail : <input type="email" name="email"> <br>
    Mot de passe : <input type="password" name="mdp"><br>
    <button type="submit">Se connecter</button>
    <a href="/passforgotten">Mot de passe oublié?</a>
</form>
<a href="index.php?controller=formulaireInscription&action=login" class="active">Pas encore inscrit ?</a>
<footer>
    <a href="/Modules/view/legal-mention.php">Mentions Légales</a>
</footer>
</body>
</html>
