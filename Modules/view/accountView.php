<?php if (isset($_SESSION['user_id'])) : ?>
    <h1>Votre compte</h1>
    <div id="info-account" class="account-info">
    <p>Vous êtes connecté au compte :
        <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></p>
    <p>Adresse mail du compte : <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    <p>Date de creation du compte : <?php echo htmlspecialchars($_SESSION['utilisateur']['created_at'] ??
                'Inconnue'); ?></p>
    <p><br></p>
    <p>Niveau du compte : <?php echo htmlspecialchars($_SESSION['level']); ?></p>
    </div>
    <form method="POST" action="index.php?controller=User&action=account">
        <ul>
            <li>
                <a href="index.php?controller=Redirection&action=openChangePwd" class="active" id="changemdp">
                    <button type="button" name="changePwd">Modifier le mot de passe</button>
                </a>
            </li>
            <?php if ($_SESSION['email'] !== 'escapethecode2025@gmail.com') : ?>
                <li>
                    <button type="submit" name="reset_progression"
                            onclick="return confirm('Confirmer le reset de votre progression ?')"
                            class="reset-progress-button">
                        Réinitialiser la progression
                    </button>
                </li>
            <?php endif; ?>
            <li>
                <button type="submit" name="delete"
                        onclick="return confirm('Confirmer la suppression ?')"
                        class="delete-account-button">
                    Supprimer ce compte
                </button>
            </li>
        </ul>
    </form>

<?php endif; ?>