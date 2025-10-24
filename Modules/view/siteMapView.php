<div class="sitemap-container">
    <h1>Plan du site</h1>
    <ul class="sitemap-list">
        <?php
        if (isset($_SESSION['utilisateur'])) {
            ?>
            <li>
                <a href="index.php?controller=redirection&action=openHomepage">Accueil</a>
                <a href="index.php?controller=user&action=logout">Déconnexion</a>
                <a href="index.php?controller=user&action=account">Compte</a>
                <a href="index.php?controller=redirection&action=openLegal">Mentions Légales</a>
                <a href="index.php?controller=redirection&action=openAbout">À propos</a>
            </li>
            <?php
        } else {
            ?>
            <li>
                <a href="index.php?controller=redirection&action=openHomepage">Accueil</a>
                <a href="index.php?controller=user&action=register">Inscription</a>
                <a href="index.php?controller=user&action=login">Connexion</a>
                <a href="index.php?controller=redirection&action=openLegal">Mentions Légales</a>
                <a href="index.php?controller=redirection&action=openAbout">À propos</a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>