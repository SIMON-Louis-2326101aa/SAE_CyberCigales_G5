<?php if (isset($_SESSION['user_id'])) : ?>
    <h1>Votre compte</h1>
    <p>Vous etes connecter au compte <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></p>

    <form method="POST" action="index.php?controller=user&action=account">
        <ul>
            <li>
                <a href="index.php?controller=redirection&action=openChangePwd" class="active" id="changemdp">
                    <button type="button" name="changePwd">Modifier le mot de passe</button>
                </a>
            </li>
            <li>
                <button type="submit" name="delete"
                        onclick="return confirm('Confirmer la suppression ?')"
                        class="danger-button">
                    Supprimer ce compte
                </button>
            </li>
        </ul>
    </form>

<?php endif; ?>