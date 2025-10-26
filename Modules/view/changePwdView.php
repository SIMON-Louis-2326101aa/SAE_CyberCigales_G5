<form method="POST" action="index.php?controller=user&action=changePwd">
    <h2>Modifier mon mot de passe</h2>

    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

    <ul>
        <li>
            <label for="new_password">Nouveau mot de passe :</label>
            <input type="password" id="new_password" name="new_password" required>
        </li>
        <li>
            <label for="confirm_password">Confirmer mot de passe :</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </li>
    </ul>

    <button type="submit" name="changePwd">Valider le changement</button>

    <a href="index.php?controller=redirection&action=openAccount" class="active">Retour</a>
</form>

