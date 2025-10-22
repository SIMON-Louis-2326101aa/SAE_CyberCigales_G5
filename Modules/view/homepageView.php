<!-- 
    Vue : Page d'accueil
    
    Cette vue affiche la page d'accueil du site.
    Elle adapte son contenu selon l'état de connexion de l'utilisateur.
    
    Si connecté :
    - Message de bienvenue personnalisé
    - Boutons Déconnexion et Compte
    
    Si non connecté :
    - Message de bienvenue générique
    - Boutons Inscription et Connexion
    
    @author SAE CyberCigales G5
    @version 1.0
-->

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Affichage pour utilisateur connecté -->
    <h1>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?> !</h1>
    <p>Vous êtes connecté(e) avec l'email : <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    
    <!-- Boutons de navigation -->
    <a href="index.php?controller=user&action=logout"><button>Déconnexion</button></a>
    <a href="index.php?controller=user&action=account"><button>Compte</button></a>

<?php else: ?>
    <!-- Affichage pour utilisateur non connecté -->
    <h1>Bienvenue</h1>
    <p>Veuillez vous connecter ou créer un compte</p>
    
    <!-- Boutons d'accès -->
    <a href="index.php?controller=user&action=register" class="active"><button>Inscription</button></a>
    <a href="index.php?controller=user&action=login" class="active"><button>Connexion</button></a>
<?php endif; ?>