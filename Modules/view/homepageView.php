<main>
<?php if (isset($_SESSION['user_id'])): ?>
    <div class="container-sm">
        <div class="card text-center">
            <h1>👋 Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?> !</h1>
            <p class="text-muted mb-6">Vous êtes connecté(e) avec l'email : <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong></p>
            
            <div class="flex-center gap-4">
                <a href="index.php?controller=user&action=account" class="btn btn-primary">
                    Mon Compte
                </a>
                <a href="index.php?controller=user&action=logout" class="btn btn-outline">
                    Déconnexion
                </a>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="container-sm">
        <div class="card text-center">
            <h1>🎮 Bienvenue sur CyberCigales</h1>
            <p class="text-secondary mb-6">
                Découvrez notre escape game numérique sur la cybersécurité et la cryptographie !<br>
                Pour commencer, veuillez vous connecter ou créer un compte.
            </p>
            
            <div class="flex-center gap-4">
                <a href="index.php?controller=user&action=register" class="btn btn-primary btn-lg">
                    📝 Créer un compte
                </a>
                <a href="index.php?controller=user&action=login" class="btn btn-secondary btn-lg">
                    🔐 Se connecter
                </a>
            </div>
        </div>
        
        <div class="card mt-6">
            <h2>🎯 À propos du projet</h2>
            <p>CyberCigales est un escape game numérique interactif destiné à initier les lycéennes aux bases de la cybersécurité et de la cryptographie de manière ludique et pédagogique.</p>
            
            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="card" style="padding: var(--space-6);">
                    <h3>🔒 Cybersécurité</h3>
                    <p class="text-muted">Apprenez les bases de la sécurité informatique</p>
                </div>
                <div class="card" style="padding: var(--space-6);">
                    <h3>🔐 Cryptographie</h3>
                    <p class="text-muted">Découvrez l'art du chiffrement</p>
                </div>
                <div class="card" style="padding: var(--space-6);">
                    <h3>🎮 Escape Game</h3>
                    <p class="text-muted">Résolvez des énigmes passionnantes</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</main>