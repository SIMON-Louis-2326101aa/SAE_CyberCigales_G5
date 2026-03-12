<div class="sitemap-container">
    <h1>Plan du site</h1>
    <ul class="sitemap-list">
        <?php
        if (isset($_SESSION['email']) && $_SESSION['email'] === $_ENV['ADMIN_EMAIL']) {
            ?>
            <li>
                <a href="index.php?controller=Redirection&action=openHomepage">Accueil</a>
                <a href="index.php?controller=Admin&action=listUsers">Tableau de bord Admin</a>
                <a href="index.php?controller=Redirection&action=openAccount">Compte</a>
                <a href="index.php?controller=Redirection&action=openLegal">Mentions Légales</a>
                <a href="index.php?controller=Redirection&action=openAbout">À propos</a>
                <br>
                <a href="index.php?controller=CrashTest&action=index">Boutons Crash Test</a>
                <a href="index.php?controller=Redirection&action=openChoice">Changer d'équipe</a>
                <br>
                <a href="index.php?controller=Redirection&action=openLetterIntro">Enigme 1 - Lettre</a>
                <a href="index.php?controller=Redirection&action=openPicturePuzzle">Enigme 2 - Photo</a>
                <a href="index.php?controller=Redirection&action=openButterflyWay">Enigme 3 - Signal</a>
                <a href="index.php?controller=Redirection&action=openPhishingPuzzle">Enigme 4 - Mail</a>
                <a href="index.php?controller=Redirection&action=openPasswordGame">Enigme 5 - Sécurité</a>
                <a href="index.php?controller=Redirection&action=openSummaryClue">Enigme 6 - Commun</a>
                <a href="index.php?controller=Redirection&action=openSearchSM">Enigme 7 - Réseau</a>
                <a href="index.php?controller=Redirection&action=openMeetingPwd">Enigme 8 - Rencontre</a>
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
                    <a href="index.php?controller=Redirection&action=openLetterIntro">Enigme 1 - Lettre</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 2)) { ?>
                    <a href="index.php?controller=Redirection&action=openPicturePuzzle">Enigme 2 - Photo</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 3)) { ?>
                    <a href="index.php?controller=Redirection&action=openButterflyWay">Enigme 3 - Signal</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 4)) { ?>
                    <a href="index.php?controller=Redirection&action=openPhishingPuzzle">Enigme 4 - Mail</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 5)) { ?>
                    <a href="index.php?controller=Redirection&action=openPasswordGame">Enigme 5 - Sécurité</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 6)) { ?>
                    <a href="index.php?controller=Redirection&action=openSummaryClue">Enigme 6 - Commun</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 7)) { ?>
                    <a href="index.php?controller=Redirection&action=openSearchSM">Enigme 7 - Réseau</a>
                <?php } ?>
                <?php if (isset($_SESSION['level']) && ($_SESSION['level'] >= 8)) { ?>
                    <a href="index.php?controller=Redirection&action=openMeetingPwd">Enigme 8 - Rencontre</a>
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