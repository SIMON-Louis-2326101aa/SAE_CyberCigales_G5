<h1> Connexion </h1>
<form action="index.php?controller=User&action=login"  method="post">
    <ul>
        <li>
            <label for="mail">E-mail :</label>
            <input type="email" id="mail" name="email" required value="<?= old('email') ?>">
        </li>
        <li>
            <label for="password">Mot de passe :</label>
            <input type="password" name="pwd" required>
        </li>
    </ul>

    <button type="submit" name="login">Se connecter</button>
    <a href="index.php?controller=Redirection&action=openForgotPwd" class="active">Mot de passe oublié?</a>
</form>
<a href="index.php?controller=Redirection&action=openFormRegister" class="active">Pas encore inscrit ?</a>
