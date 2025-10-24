<main>
    <div class="container-sm">
        <div class="card">
            <h1 class="text-center">🔐 Connexion</h1>
            <p class="text-center text-muted mb-6">Connectez-vous pour accéder à l'escape game</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <strong>⚠️ Erreur :</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($info)): ?>
                <div class="alert alert-info">
                    <strong>ℹ️ Information :</strong> <?= htmlspecialchars($info) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <strong>✅ Succès :</strong> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=user&action=login" method="post">
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="pwd">Mot de passe</label>
                    <input type="password" id="pwd" name="pwd" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">
                    Se connecter
                </button>

                <div class="text-center mt-4">
                    <a href="index.php?controller=user&action=forgot" class="text-secondary">Mot de passe oublié ?</a>
                </div>
            </form>

            <hr style="margin: var(--space-6) 0; border: none; border-top: 1px solid var(--border);">

            <p class="text-center text-muted">
                Pas encore inscrit ? 
                <a href="index.php?controller=user&action=register" class="text-primary" style="font-weight: 600;">Créer un compte</a>
            </p>
        </div>
    </div>
</main>
