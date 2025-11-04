    <h1>Inscription</h1>

    <form action="index.php?controller=User&action=register" method="post">
        <ul>
            <li>
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required value="<?= old('nom') ?>">
            </li>
            <li>
                <label for="prenom">Prenom :</label>
                <input type="text" id="prenom" name="prenom" required value="<?= old('prenom') ?>">
            </li>
            <li>
                <label for="mail">E-mail :</label>
                <input type="email" id="mail" name="email" required value="<?= old('email') ?>">
            </li>
            <li>
                <label for="password">Mot de passe :</label>
                <input type="password" id="pwd" name="pwd" required>
            </li>
            <li>
                <label for="confirm_mdp">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_pwd" name="confirm_pwd" required>
            </li>
        </ul>

        <button type="submit" name="register">S'inscrire</button><br>

        <a href="index.php?controller=Redirection&action=openFormConnection" class="active">Dejà un compte ? Connexion</a>
    </form>
