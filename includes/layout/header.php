<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $A_params['pageTitle'] ?? 'CyberCigales'; ?></title>
    <link rel="stylesheet" href="./assets/styles/stylesheet.css">
    <link rel="icon" href="./assets/images/favicon.ico">
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav>
    <a href="index.php?controller=redirection&action=openHomepage" class="active">Home</a>
</nav>

<!-- Permet d'ajouter les pop up flash dans le header sans trop gêner la page-->
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="flash flash-success">
        <?= htmlspecialchars($_SESSION['flash_success']); ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="flash flash-error">
        <?= htmlspecialchars($_SESSION['flash_error']); ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>