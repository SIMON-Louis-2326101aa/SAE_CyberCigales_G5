<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $A_params['pageTitle'] ?? 'CyberCigales - Escape Game Cybersécurité'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/styles/stylesheet.css">
    <link rel="icon" href="./assets/images/favicon.ico">
</head>
<body>
<nav class="nav-menu">
    <div class="nav-container">
        <a href="index.php?controller=redirection&action=openHomepage" class="nav-logo">🔐 CyberCigales</a>
        
        <div id="nav-menu-sections">
            <a href="index.php?controller=redirection&action=openHomepage">Accueil</a>
        </div>

        <div id="nav-auth-buttons">
            <a href="index.php?controller=user&action=register">Inscription</a>
            <a href="index.php?controller=user&action=login">Connexion</a>
        </div>
    </div>
</nav>
