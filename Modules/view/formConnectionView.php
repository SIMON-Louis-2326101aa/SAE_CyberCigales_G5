<!-- 
    Vue : Formulaire de connexion
    
    Cette vue affiche le formulaire de connexion utilisateur.
    
    Fonctionnalités :
    - Champ email (requis)
    - Champ mot de passe (requis)
    - Lien vers mot de passe oublié
    - Lien vers inscription
    
    @author SAE CyberCigales G5
    @version 1.0
-->

<h1> Connection </h1>

<!-- Formulaire de connexion - envoie vers userController->login() -->
<form action="index.php?controller=user&action=login"  method="post">
    <!-- Champ email -->
    E-mail : <input type="email" name="email" required> <br>
    
    <!-- Champ mot de passe -->
    Mot de passe : <input type="password" name="pwd" required><br>
    
    <!-- Bouton de soumission -->
    <button type="submit" name="login">Se connecter</button>
    
    <!-- Lien vers récupération de mot de passe -->
    <a href="index.php?controller=user&action=forgot" class="active">Mot de passe oublié?</a>
</form>

<!-- Lien vers inscription pour les nouveaux utilisateurs -->
<a href="index.php?controller=user&action=register" class="active">Pas encore inscrit ?</a>
