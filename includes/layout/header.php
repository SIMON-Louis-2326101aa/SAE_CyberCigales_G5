<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $A_params['pageTitle'] ?? 'Escape The Code '; ?></title>
    <link rel="stylesheet" href="./assets/styles/stylesheet.css">
    <link rel="stylesheet" href="./assets/styles/puzzle.css">
    <link rel="icon" href="./assets/images/favicon.ico">
    <script src="./assets/js/script.js" defer></script>
    <script src="./assets/js/puzzle.js" defer></script>
    <?php if (isset($_GET['controller']) && $_GET['controller'] === 'Admin') : ?>
        <!-- Si Admin, alors le script pour admin est chargé -->
        <script src="./assets/js/admin.js" defer></script>
    <?php endif; ?>
</head>
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

    <div class="theme-switcher-container">
        <label for="theme-selector" class="visually-hidden">Choisir un thème</label>
        <select id="theme-selector">
            <option value="default">Thème par défaut</option>
            <option value="deuteranopia-protanopia">Deutéranopie-Protanopie (Rouge-Vert)</option>
            <option value="tritanopia">Tritanopie (Bleu-Jaune)</option>
        </select>
    </div>
</nav>

<?php
if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
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