<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $A_params['pageTitle'] ?? 'CyberCigales'; ?></title>
    <link rel="stylesheet" href="./assets/styles/stylesheet.css">
    <link rel="icon" href="./assets/images/favicon.ico">
</head>
<body>
<nav class="nav-menu">

    <div id="nav-menu-sections">
        <a href="index.php?controller=redirection&action=openHomepage" class="active">Home</a>
    </div>

    <div id="nav-auth-buttons">
        <?php
        if (isset($_SESSION['utilisateur'])) {
            ?>
            <a href="index.php?controller=user&action=logout" class="active">Déconnexion</a>
            <a href="index.php?controller=user&action=account" class="active">Compte</a>
            <?php
        } else {
            ?>
            <a href="index.php?controller=user&action=register" class="active">Inscription</a>
            <a href="index.php?controller=user&action=login" class="active">Connexion</a>
            <?php
        }
        ?>
    </div>

</nav>