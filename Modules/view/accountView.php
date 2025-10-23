<?php
// Vérifier si l'utilisateur est admin
$isAdmin = isset($_SESSION['email']) && $_SESSION['email'] === 'escapethecode2025@gmail.com';
?>

<?php if ($isAdmin): ?>
<h1>Bonjour Admin !</h1>
<p>Voici la base de donnée : <?php
    // Récupérer tous les utilisateurs avec SQL direct
    require_once __DIR__ . '/../../includes/connectionDB.php';
    $db = connectionDB::getInstance();
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
<a href="index.php?controller=user&action=logout"><button>Déconnexion</button></a>

<?php elseif (isset($_SESSION['user_id'])) : ?>
    <h1>Votre compte</h1>
    <p>Vous etes connecter au compte <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></p>

    <form method="POST" action="index.php?controller=user&action=account">
        <ul>
            <li>
                <a href="index.php?controller=user&action=changePwd" class="active" id="changemdp">
                    <button type="button" name="changePwd">Modifier le mot de passe</button>
                </a>
            </li>
            <li>
                <button type="submit" name="delete"
                        onclick="return confirm('Confirmer la suppression ?')"
                        class="danger-button">
                    Supprimer ce compte
                </button>
            </li>
        </ul>
    </form>

<?php endif; ?>