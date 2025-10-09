<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
    <link rel="icon" href="/public/assets/images/favicon.ico">
</head>
<body>
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
<?php elseif (isset($_SESSION['user_id'])): ?>
    <h1>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?> !</h1>
    <p>Vous êtes connecté(e) avec l'email : <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    <a href="index.php?controller=formConnection&action=logout"><button>Déconnexion</button></a>

<?php else: ?>
    <h1>Bienvenue</h1>
    <p>Veuillez vous connecter ou créer un compte</p>
    <a href="index.php?controller=formInscription&action=register" class="active"><button>Inscription</button></a>
    <a href="index.php?controller=formConnection&action=login" class="active"><button>Connexion</button></a>
<?php endif; ?>

<footer>
    <a href="index.php?controller=legalMention&action=legal" class="active">Mentions Légales</a>
</footer>

</body>
</html>