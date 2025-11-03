<footer class="footer">
    <a href="index.php?controller=Redirection&action=openLegal" class="active" id="footer-legal">Mentions Légales</a>
    <a href="index.php?controller=Redirection&action=openAbout" class="active" id="footer-about">À propos</a>
    <a href="index.php?controller=Redirection&action=openSiteMap" class="active" id="footer-sitemap">Plan du Site</a>
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
</body>
</html>
