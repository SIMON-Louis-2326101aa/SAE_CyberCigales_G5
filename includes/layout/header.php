<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $A_params['pageTitle'] ?? 'Escape The Code '; ?></title>
    <link rel="stylesheet" href="./assets/styles/stylesheet.css">
    <link rel="icon" href="./assets/images/favicon.ico">
    <script src="./assets/js/script.js"></script>
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="nav-menu">

    <div id="nav-menu-sections">
        <a href="index.php?controller=redirection&action=openHomepage" id="nav-menu-title"><img src="./assets/images/logo.png" alt="logo-escape-the-code"></a>
        <a href="index.php?controller=redirection&action=openHomepage" class="active btn-nav">Accueil</a>
    </div>

    <div id="nav-auth-buttons">
        <?php
        if (isset($_SESSION['utilisateur'])) {
            ?>
            <!-- Changer de Theme marche comme un bouton mais l'ajout de javascript:void(0) permet de ne pas recharger la page -->
            <a href="javascript:void(0)" id="theme-changer" class="btn-nav">Changer de Theme</a>
            <a href="index.php?controller=user&action=logout" class="active btn-nav">Déconnexion</a>
            <a href="index.php?controller=redirection&action=openAccount" class="active btn-nav">Compte</a>
            <?php
        } else {
            ?>
            <!-- Changer de Theme d(0) -->
            <a href="javascript:void(0)" id="theme-changer" class="btn-nav">Changer de Theme</a>
            <a href="index.php?controller=redirection&action=openFormRegister" class="active btn-nav">Inscription</a>
            <a href="index.php?controller=redirection&action=openFormConnection" class="active btn-nav">Connexion</a>
            <?php
        }
        ?>
    </div>
</nav>

<?php
if (!function_exists('old')) {
    function old(string $key, string $default = ''): string {
        $val = $_SESSION['old'][$key] ?? $default;

        // Normalisation & nettoyage agressif
        $val = (string)$val;
        $val = strip_tags($val);                 // vire <script>...</script> & toutes balises
        $val = preg_replace('/[\x00-\x1F\x7F]/', '', $val); // caractères de contrôle
        $val = preg_replace('/\s+/', ' ', $val); // espaces multiples
        $val = trim($val);
        $val = mb_substr($val, 0, 120);         // limite défensive

        // Sortie sûre pour un attribut HTML
        return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
    }
}
?>

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

<?php if (!empty($_SESSION['flash_info'])): ?>
    <div class="flash flash-info">
        <?= htmlspecialchars($_SESSION['flash_info']); ?>
    </div>
    <?php unset($_SESSION['flash_info']); ?>
<?php endif; ?>