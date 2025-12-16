<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $A_params['pageTitle'] ?? 'Escape The Code '; ?></title>
    <link rel="stylesheet" href="./assets/styles/stylesheet.css">
    <link rel="stylesheet" href="./assets/styles/puzzle.css">
    <link rel="icon" href="./assets/images/faviconBis.ico">
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/puzzle.js"></script>
    <?php if (isset($_GET['controller']) && $_GET['controller'] === 'Admin') : ?>
        <!-- Si Admin, alors le script pour admin est chargé -->
        <script src="./assets/js/admin.js"></script>
    <?php endif; ?>
</head>
<?php if (isset($_SESSION['game_start_time'])) : ?>
    <script>
        const GAME_START_TIME = <?= $_SESSION['game_start_time'] ?> * 1000;
    </script>
<?php endif; ?>

<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="nav-menu">

    <div id="nav-menu-sections">
        <a href="index.php?controller=Redirection&action=openHomepage" id="nav-menu-title">
            <img src="./assets/images/logoBis.webp" alt="logo-escape-the-code"></a>
        <h5><strong>Escape The Code</strong></h5>
    </div>

    <?php if (isset($_SESSION['game_start_time'])) : ?>
        <div id="game-timer">
            Chrono : <span id="time-display">00:00:00</span>
        </div>
    <?php endif; ?>

    <div id="nav-auth-buttons">
        <?php
        if (isset($_SESSION['utilisateur'])) {
            ?>
            <a href="index.php?controller=Redirection&action=openHomepage" class="active btn-nav">Accueil</a>
            <a href="index.php?controller=User&action=logout" class="active btn-nav">Déconnexion</a>
            <a href="index.php?controller=Redirection&action=openAccount" class="active btn-nav">Compte</a>
            <?php if (isset($_SESSION['email']) && $_SESSION['email'] === 'escapethecode2025@gmail.com') : ?>
                <!-- Si Admin, alors le bouton Admin est accessible -->
                <a href="index.php?controller=Admin&action=listUsers" class="active btn-nav" id="admin-button">Admin</a>
            <?php endif; ?>
            <?php
        } else {
            ?>
            <a href="index.php?controller=Redirection&action=openHomepage" class="active btn-nav">Accueil</a>
            <a href="index.php?controller=Redirection&action=openFormRegister" class="active btn-nav">Inscription</a>
            <a href="index.php?controller=Redirection&action=openFormConnection" class="active btn-nav">Connexion</a>
            <?php
        }
        ?>
    </div>
</nav>


<!-- Permet d'ajouter les pop up flash dans le header sans trop gêner la page-->
<?php if (!empty($_SESSION['flash_success'])) : ?>
    <div class="flash flash-success">
        <?= htmlspecialchars($_SESSION['flash_success']); ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])) : ?>
    <div class="flash flash-error">
        <?= htmlspecialchars($_SESSION['flash_error']); ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>