<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
</head>

<body>
<form action="/passforgotten" method="post">
	<h2>Vous avez oublié le mot de passe</h2>
	<h3>Veuillez mettre votre Email pour réinitialiser votre mot de passe</h3>
        <ul>
            <li>
                <label for="mail">E-mail :</label>
                <input type="email" id="mail" name="email" required>
            </li>
        </ul>
        <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>
		<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <button type="submit">Réinitialiser le mot de passe</button>
    </form>
    <p><a href="/login">Retour</a></p>
</body>
</html>
