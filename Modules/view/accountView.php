<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
    <link rel="icon" href="/public/assets/images/favicon.ico">
</head>
<nav>
    <a href="index.php?controller=homepage&action=openHomepage" class="active">Home</a>
</nav>
<?php
// Vérifier si l'utilisateur est admin
$isAdmin = isset($_SESSION['email']) && $_SESSION['email'] === 'escapethecode2025@gmail.com';
?>

<?php if ($isAdmin): ?>
<h1>Bonjour Admin !</h1>
<p>Voici la base de donnée : <?php
    // Récupérer tous les utilisateurs avec SQL direct
    require_once __DIR__ . '/../../includes/connexionDB.php';
    $db = connexionDB::getInstance();
    $pdo = $db->getPdo();

    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll();
    ?></p>
<table class="db-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Date d'inscription</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['nom']); ?></td>
            <td><?php echo htmlspecialchars($user['prenom']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table></p>
<a href="index.php?controller=formConnection&action=logout"><button>Déconnexion</button></a>

<?php elseif (isset($_SESSION['user_id'])) : ?>
    <h1>Votre compte</h1>
    <p>Vous etes connecter au compte <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></p>
    <a href="index.php?controller=account&action=delete" class="active"><button>Suppremier ce compte</button></a>
    <a href="index.php?controller=account&action=changePwd" class="active"><button>Modifier le mot de passe</button></a>
<?php endif; ?>
<footer>
    <a href="index.php?controller=legalMention&action=legal" class="active">Mentions Légales</a>
</footer>

</body>
</html>