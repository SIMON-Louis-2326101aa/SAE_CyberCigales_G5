<?php if (isset($_SESSION['user_id'])): ?>
    <div class="hero-container">
        <img src="/assets/images/img.png" alt="Arrière-plan de la page d'accueil" loading="lazy">

        <h1>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?> !</h1>
        <p>Vous êtes connecté(e) avec l'email : <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    </div>

<?php else: ?>
    <div class="hero-container">
        <img src="/assets/images/img.png" alt="Arrière-plan de la page d'accueil" loading="lazy">

        <h1>Bienvenue</h1>

        <div>

        </div>
    </div>
<?php endif; ?>