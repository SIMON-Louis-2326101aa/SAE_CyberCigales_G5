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
        <a href="index.php?controller=redirection&action=openHomepage" class="active btn-nav">Home</a>
    </div>

    <div id="nav-auth-buttons">
        <!-- Changer de Theme marche comme un bouton mais l'ajout de javascript:void(0) permet de ne pas recharger la page -->
        <a href="javascript:void(0)" id="theme-changer" class="btn-nav">Changer de Theme</a>
        <a href="index.php?controller=user&action=register" class="active btn-nav">Inscription</a>
        <a href="index.php?controller=user&action=login" class="active btn-nav">Connexion</a>
    </div>

</nav>
