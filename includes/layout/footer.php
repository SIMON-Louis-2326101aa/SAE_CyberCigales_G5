<footer class="footer">
    <div class="footer-top">
        <!-- Colonne 1: À propos -->
        <div class="footer-column">
            <h3>ESCAPETHECODE</h3>
            <p>Explorez les mystères des anciens temples mayas à travers six épreuves immersives. Une aventure unique vous attend dans les profondeurs de la jungle mystique.</p>
        </div>

        <!-- Colonne 2: Navigation -->
        <div class="footer-column">
            <h3>Navigation</h3>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="index.php?controller=redirection&action=openSiteMap">Plan du site</a></li>
                <li><a href="index.php?controller=contact&action=show">Contact</a></li>
            </ul>
        </div>

        <!-- Colonne 3: Réseaux sociaux -->
        <div class="footer-column">
            <h3>Nos réseaux</h3>
            <div class="social-icons">
                <a href="#" class="container-link-footer">
                    <img class='footer-img' src="/assets/images/instagram-logo.webp" alt="Instagram">
                </a>
                <a href="#" class="container-link-footer">
                    <img class='footer-img' src="/assets/images/twitter-logo.webp" alt="Twitter">
                </a>
                <a href="#" class="container-link-footer">
                    <img class="footer-img" src="/assets/images/logo-tiktok.webp" alt="TikTok">
                </a>
            </div>
        </div>
    </div>

    <!-- Ligne de séparation -->
    <div class="footer-separator"></div>

    <!-- Bas du footer -->
    <div class="footer-bottom">
        <div class="footer-legal">
            <a href="index.php?controller=redirection&action=openLegal">Mentions légales</a>
            <a href="index.php?controller=contact&action=show">Contact</a>
            <a href="index.php?controller=redirection&action=openAbout">À propos</a>
        </div>
        <div class="footer-copyright">
            © 2025 ESCAPETHECODE. Tous droits réservés.
        </div>
    </div>
</footer>
<?php
// Rejoue le buffer de logs si tu l'utilises
if (!empty($GLOBALS['dev_log_buffer'])) {
    echo "<script>(function(){";
    foreach ($GLOBALS['dev_log_buffer'] as $row) {
        $msg = json_encode($row['msg'], JSON_UNESCAPED_UNICODE);
        $color = json_encode('color: ' . $row['color']);
        echo "try{console.log('%c'+$msg, $color);}catch(e){}";
    }
    echo "})();</script>";
    unset($GLOBALS['dev_log_buffer']);
}

// On peut purger 'old' après affichage pour éviter la persistance
if (!empty($_SESSION['old'])) {
    unset($_SESSION['old']);
}
?>
