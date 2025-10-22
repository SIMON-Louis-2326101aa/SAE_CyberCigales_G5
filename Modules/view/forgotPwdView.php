<!-- 
    Vue : Formulaire mot de passe oublié
    
    Cette vue affiche le formulaire de réinitialisation de mot de passe.
    
    Fonctionnalités :
    - Saisie de l'email
    - Envoi d'un email avec lien de réinitialisation
    - Lien retour vers la connexion
    
    @author SAE CyberCigales G5
    @version 1.0
-->

<!-- Formulaire de réinitialisation - envoie vers userController->forgot() -->
<form action="index.php?controller=user&action=forgot" method="post">
	<h2>Vous avez oublié votre mot de passe</h2>
	<h3>Veuillez mettre votre Email pour réinitialiser votre mot de passe</h3>

	<!-- Champ email -->
	<label for="mail">E-mail :</label>
	<input type="email" id="mail" name="email" required><br>
	
	<!-- Bouton de soumission -->
	<button type="submit" name="forgotPwd">Réinitialiser le mot de passe</button>
</form>

<!-- Lien de retour vers la connexion -->
<p><a href="index.php?controller=user&action=login" class="active">Retour</a></p>
