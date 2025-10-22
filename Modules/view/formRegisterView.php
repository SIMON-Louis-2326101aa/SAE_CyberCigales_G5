<!-- 
    Vue : Formulaire d'inscription
    
    Cette vue affiche le formulaire d'inscription pour les nouveaux utilisateurs.
    
    Fonctionnalités :
    - Saisie nom et prénom
    - Saisie email (unique)
    - Saisie et confirmation du mot de passe
    - Validation côté serveur (longueur, complexité)
    
    @author SAE CyberCigales G5
    @version 1.0
-->

<h1>Inscription</h1>

<!-- Formulaire d'inscription - envoie vers userController->register() -->
<form action="index.php?controller=user&action=register" method="post">
    
    <!-- Champ nom de famille -->
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required><br>

    <!-- Champ prénom -->
    <label for="prenom">Prenom :</label>
    <input type="text" id="prenom" name="prenom" required><br>

    <!-- Champ email (doit être unique) -->
    <label for="mail">E-mail :</label>
    <input type="email" id="mail" name="email" required><br>

    <!-- Champ mot de passe (min 8 car, avec maj, min, chiffre, spécial) -->
    <label for="password">Mot de passe :</label>
    <input type="password" id="pwd" name="pwd" required><br>

    <!-- Confirmation du mot de passe -->
    <label for="confirm_mdp">Confirmer le mot de passe :</label>
    <input type="password" id="confirm_pwd" name="confirm_pwd" required><br>

    <!-- Bouton de soumission -->
    <button type="submit" name="register">S'inscrire</button>
</form>

<!-- Lien vers connexion pour les utilisateurs existants -->
<a href="index.php?controller=user&action=login" class="active">Dejà un compte ? Connection</a>