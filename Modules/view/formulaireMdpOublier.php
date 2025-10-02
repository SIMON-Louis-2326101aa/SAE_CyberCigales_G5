<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
</head>

<body>
<form action="/register" method="post">
	<h2>Vous avez oublié le mot de passe</h2>
	<h3>Veuillez mettre votre Email pour réinitialiser votre mot de passe</h3>
        <ul>
            <li>
                <label for="mail">E-mail :</label>
                <input type="email" id="mail" name="email" required>
            </li>
        </ul>
        <button type="submit">Envoyer un Email</button>
    </form>
</body>
</html>
