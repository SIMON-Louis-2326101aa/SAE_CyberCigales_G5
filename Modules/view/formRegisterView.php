<h1>Inscription</h1>
<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form action="index.php?controller=user&action=register" method="post">

    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($nom ?? '') ?>"><br>

    <label for="prenom">Prenom :</label>
    <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($prenom ?? '') ?>"><br>

    <label for="mail">E-mail :</label>
    <input type="email" id="mail" name="email" required value="<?= htmlspecialchars($email ?? '') ?>"><br>

    <label for="password">Mot de passe :</label>
    <input type="password" id="pwd" name="pwd" required><br>

    <label for="confirm_mdp">Confirmer le mot de passe :</label>
    <input type="password" id="confirm_pwd" name="confirm_pwd" required><br>


    <button type="submit" name="register">S'inscrire</button>
</form>
<a href="index.php?controller=user&action=login" class="active">Dejà un compte ? Connection</a>