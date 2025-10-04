<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
    <link rel="icon" type="image/png" href="/public/assets/Image/favicon.png">
</head>
<body>
<?php if (isset($_SESSION['user_id'])): ?>
    <h1>Bienvenue <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></h1>
    <a href="/logout"><button>Deconnexion</button></a>
<?php else: ?>
    <h1>Bienvenue</h1>
    <a href="/register"><button>Inscription</button></a>
    <a href="/login"><button>Connexion</button></a>
<?php endif; ?>

<footer>
    <a href="legal-mention.php">Mentions Légales</a>
</footer>

</body>
</html>
