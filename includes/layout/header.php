<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $A_params['pageTitle'] ?? 'CyberCigales'; ?></title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
    <link rel="icon" href="/public/assets/images/favicon.ico">
</head>
<body>
<nav>
    <a href="index.php?controller=redirection&action=openHomepage" class="active">Home</a>
    <a href="index.php?controller=redirection&action=openAbout">À Propos</a>
    <a href="index.php?controller=redirection&action=openLegal">Mentions Légales</a>
</nav>
