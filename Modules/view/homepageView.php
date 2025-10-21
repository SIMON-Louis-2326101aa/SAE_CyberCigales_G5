<?php if (isset($_SESSION['user_id'])): ?>
    <h1>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?> !</h1>
    <p>Vous êtes connecté(e) avec l'email : <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    <a href="index.php?controller=user&action=logout"><button>Déconnexion</button></a>
    <a href="index.php?controller=user&action=account"><button>Compte</button></a>

<?php else: ?>
    <h1>Bienvenue</h1>
    <p>Veuillez vous connecter ou créer un compte</p>
    <a href="index.php?controller=user&action=register" class="active"><button>Inscription</button></a>
    <a href="index.php?controller=user&action=login" class="active"><button>Connexion</button></a>
<?php endif; ?>