<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $A_params['pageTitle'] ?? 'CyberCigales'; ?></title>
    <link rel="stylesheet" href="./assets/styles/stylesheet.css">
    <link rel="icon" href="./assets/images/favicon.ico">
    <script src="/assets/js/JSVide.js"></script>
</head>
<body>
<nav class="nav-menu">

    <div id="nav-menu-sections">
        <a href="index.php?controller=redirection&action=openHomepage" class="active theme-button">Home</a>
    </div>

    <div id="nav-auth-buttons">
        <button class="theme-button">Changer de Theme</button>
        <a href="index.php?controller=user&action=register" class="active"><button>Inscription</button></a>
        <a href="index.php?controller=user&action=login" class="active"><button>Connexion</button></a>
    </div>

</nav>
