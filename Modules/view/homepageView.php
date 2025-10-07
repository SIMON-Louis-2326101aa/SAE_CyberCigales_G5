<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
    <link rel="icon" href="/public/assets/images/favicon.ico">
</head>
<body>
<?php if (isset($_SESSION['user_id'])): ?>
    <h1>Bienvenue <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></h1>
    <a href="/logout"><button>Deconnexion</button></a>
<?php else: ?>
    <h1>Bienvenue</h1>
    <a href="index.php?controller=formInscription&action=login" class="active"><button>Inscription</button></a>
    <a href="index.php?controller=formConnection&action=login" class="active"><button>Connection</button></a>
<?php endif; ?>

<footer>
    <a href="index.php?controller=legalMention&action=login" class="active">Mentions Légales</a>
</footer>

</body>
</html>
