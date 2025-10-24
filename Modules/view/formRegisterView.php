<main>
    <div class="container-sm">
        <div class="card">
            <h1 class="text-center">📝 Inscription</h1>
            <p class="text-center text-muted mb-6">Créez votre compte pour rejoindre l'aventure CyberCigales</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <strong>⚠️ Erreur :</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=user&action=register" method="post">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" placeholder="Votre nom" required value="<?= htmlspecialchars($nom ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required value="<?= htmlspecialchars($prenom ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="mail">Adresse e-mail</label>
                    <input type="email" id="mail" name="email" placeholder="votre@email.com" required value="<?= htmlspecialchars($email ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="pwd">Mot de passe</label>
                    <input type="password" id="pwd" name="pwd" placeholder="••••••••" required>
                    <small class="text-muted">Minimum 8 caractères</small>
                </div>

                <div class="form-group">
                    <label for="confirm_pwd">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_pwd" name="confirm_pwd" placeholder="••••••••" required>
                </div>

                <button type="submit" name="register" class="btn btn-primary" style="width: 100%;">
                    Créer mon compte
                </button>
            </form>

            <hr style="margin: var(--space-6) 0; border: none; border-top: 1px solid var(--border);">

            <p class="text-center text-muted">
                Déjà un compte ? 
                <a href="index.php?controller=user&action=login" class="text-primary" style="font-weight: 600;">Se connecter</a>
            </p>
        </div>
    </div>
</main>