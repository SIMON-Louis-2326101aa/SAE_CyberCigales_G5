<!-- 
    Vue : Page de gestion de compte
    
    Cette vue affiche les informations du compte utilisateur avec deux vues différentes :
    
    1. Vue Admin (si email = escapethecode2025@gmail.com) :
       - Affiche tous les utilisateurs de la base de données
       - Tableau complet avec ID, nom, prénom, email, date d'inscription
    
    2. Vue Utilisateur standard :
       - Affiche les informations du compte
       - Bouton de suppression de compte (avec confirmation)
       - Lien vers modification du mot de passe
    
    @author SAE CyberCigales G5
    @version 1.0
-->

<?php
// Vérification des privilèges admin (email spécifique)
$isAdmin = isset($_SESSION['email']) && $_SESSION['email'] === 'escapethecode2025@gmail.com';
?>

<?php if ($isAdmin): ?>
    <!-- Vue Admin : Tableau de tous les utilisateurs -->
    <h1>Bonjour Admin !</h1>
    <p>Voici la base de donnée : 
    <?php
        // Récupération de tous les utilisateurs depuis la base de données
        require_once __DIR__ . '/../../includes/connectionDB.php';
        $db = connectionDB::getInstance();
        $pdo = $db->getPdo();

        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll();
    ?>
    </p>
    
    <!-- Tableau des utilisateurs -->
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
                <!-- Affichage sécurisé des données utilisateur -->
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </p>
    <a href="index.php?controller=user&action=logout"><button>Déconnexion</button></a>

<?php elseif (isset($_SESSION['user_id'])) : ?>
    <!-- Vue Utilisateur : Gestion du compte -->
    <h1>Votre compte</h1>
    <p>Vous etes connecter au compte <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></p>
    
    <!-- Formulaire de suppression de compte -->
    <form method="POST" action="index.php?controller=user&action=account">
        <button type="submit" name="delete"
                onclick="return confirm('Confirmer la suppression ?')"
                class="danger-button">
            Supprimer ce compte
        </button>
    </form>
    
    <!-- Lien vers modification du mot de passe -->
    <a href="index.php?controller=user&action=changePwd" class="active"><button name="changePwd">Modifier le mot de passe</button></a>
<?php endif; ?>