    <h1>Inscription</h1>

    <form action="index.php?controller=formRegister&action=register" method="post">

                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required><br>

                <label for="prenom">Prenom :</label>
                <input type="text" id="prenom" name="prenom" required><br>

                <label for="mail">E-mail :</label>
                <input type="email" id="mail" name="email" required><br>

                <label for="password">Mot de passe :</label>
                <input type="password" id="pwd" name="pwd" required><br>

                <label for="confirm_mdp">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_pwd" name="confirm_pwd" required><br>


        <button type="submit" name="register">S'inscrire</button>
    </form>
    <a href="index.php?controller=formConnection&action=login" class="active">Dejà un compte ? Connection</a>