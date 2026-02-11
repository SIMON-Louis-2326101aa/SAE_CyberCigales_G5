<div class="sitemap-container">
    <h1>Plan du site</h1>
    <ul class="sitemap-list">
        <?php
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 5) {
            ?>
            <li>
                <a href="index.php?controller=Redirection&action=openHomepage">Accueil</a>
                <a href="index.php?controller=Admin&action=listUsers">Tableau de bord Admin</a>
                <a href="index.php?controller=Redirection&action=openAccount">Compte</a>
                <a href="index.php?controller=Redirection&action=openLegal">Mentions Légales</a>
                <a href="index.php?controller=Redirection&action=openAbout">À propos</a>
                <br>
                <a href="index.php?controller=Redirection&action=openChoice">Changer d'équipe</a>
                <a href="index.php?controller=Redirection&action=openLetterIntro">Enigme 1</a>
                <a href="index.php?controller=Redirection&action=openPicturePuzzle">Enigme 2</a>
                <a href="index.php?controller=Redirection&action=openButterflyWay">Enigme 3</a>
                <a href="index.php?controller=Redirection&action=openPhishingPuzzle">Enigme 4</a>
                <a href="index.php?controller=Redirection&action=openPasswordGame">Enigme 5</a>
                <a href="index.php?controller=Redirection&action=openSummaryClue">Enigme 6</a>
                <a href="index.php?controller=Redirection&action=openSearchSM">Enigme 7</a>
                <a href="index.php?controller=Redirection&action=openMeetingPwd">Enigme 8</a>
                <a href="index.php?controller=Redirection&action=openEndText">Enigme Finale</a>
            </li>
            <?php
        } elseif (isset($_SESSION['utilisateur'])) {
            ?>
            <li>
                <a href="index.php?controller=Redirection&action=openHomepage">Accueil</a>
                <a href="index.php?controller=Redirection&action=openAccount">Compte</a>
                <a href="index.php?controller=Redirection&action=openLegal">Mentions Légales</a>
                <a href="index.php?controller=Redirection&action=openAbout">À propos</a>
                <br>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 1)) { ?>
                    <a href="index.php?controller=Redirection&action=openLetterIntro">Enigme 1</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 2)) { ?>
                    <a href="index.php?controller=Redirection&action=openPicturePuzzle">Enigme 2</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 3)) { ?>
                    <a href="index.php?controller=Redirection&action=openButterflyWay">Enigme 3</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 4)) { ?>
                    <a href="index.php?controller=Redirection&action=openPhishingPuzzle">Enigme 4</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 5)) { ?>
                    <a href="index.php?controller=Redirection&action=openPasswordGame">Enigme 5</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 6)) { ?>
                    <a href="index.php?controller=Redirection&action=openSummaryClue">Enigme 6</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 7)) { ?>
                    <a href="index.php?controller=Redirection&action=openSearchSM">Enigme 7</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 8)) { ?>
                    <a href="index.php?controller=Redirection&action=openMeetingPwd">Enigme 8</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 9)) { ?>
                    <a href="index.php?controller=Redirection&action=openEndText">Enigme Finale</a>
                <?php } ?>
            </li>
            <?php
        } else {
            ?>
            <li>
                <a href="index.php?controller=Redirection&action=openHomepage">Accueil</a>
                <a href="index.php?controller=Redirection&action=openFormRegister">Inscription</a>
                <a href="index.php?controller=Redirection&action=openLogin">Connexion</a>
                <a href="index.php?controller=Redirection&action=openLegal">Mentions Légales</a>
                <a href="index.php?controller=Redirection&action=openAbout">À propos</a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>