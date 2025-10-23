<form action="index.php?controller=user&action=forgot" method="post">
	<h2>Vous avez oublié votre mot de passe</h2>
	<h3>Veuillez mettre votre E-mail pour réinitialiser votre mot de passe</h3>

        <ul>
            <li>
                <label for="mail">E-mail :</label>
                <input type="email" id="mail" name="email" required>
            </li>
        </ul>

        <button type="submit" name="forgotPwd">Réinitialiser le mot de passe</button>

    </form>
    <p><a href="index.php?controller=user&action=login" class="active">Retour</a></p>
