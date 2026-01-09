<?php if (isset($_SESSION['user_id'])) : ?>
    <div class="hero-container-welcome">
        <h1>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?> !</h1>
        <p>Vous êtes connecté(e) avec l'email : <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <br>
        <p><strong>Certains secrets ne se lisent pas, ils se déchiffrent.
                Derrière chaque symbole, chaque message ou ligne de code, se cache une vérité à découvrir.
                Entre cryptographie et cybersécurité, ton esprit est ta meilleure clé.
                Décrypte. Analyse. Protège.
            </strong></p>
        <p><strong>L’aventure commence ici.</strong></p>
    </div>

    <div class="hero-container-main">
        <h1 class="hero-question">Serez-vous capable de résoudre le mystère ?</h1>
        <a  href='index.php?controller=Team&action=play'
            class="active btn-nav">JOUER</a>
    </div>

<?php else : ?>
    <div class="hero-container-welcome">
        <h1>Bienvenue sur Escape The Code</h1>

        <p><strong>Certains secrets ne se lisent pas, ils se déchiffrent.
                Derrière chaque symbole, chaque message ou ligne de code, se cache une vérité à découvrir.
                Entre cryptographie et cybersécurité, ton esprit est ta meilleure clé.
                Décrypte. Analyse. Protège.
            </strong></p>
        <br>
        <p><strong>L’aventure commence ici.</strong></p>
    </div>

    <div class="hero-container-main">
        <h1 class="hero-question">Serez-vous capable de résoudre le mystère ?</h1>
        <a  href="index.php?controller=Redirection&action=openFormConnection"
            class="active btn-nav">SE CONNECTER POUR JOUER</a>
    </div>
<?php endif; ?>