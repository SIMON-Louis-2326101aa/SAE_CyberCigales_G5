<!-- 
    Vue : Formulaire de changement de mot de passe
    
    Cette vue affiche le formulaire pour changer le mot de passe.
    
    Fonctionnalités :
    - Saisie du nouveau mot de passe
    - Confirmation du nouveau mot de passe
    - Validation côté serveur (longueur min 8 car, complexité)
    
    @author SAE CyberCigales G5
    @version 1.0
-->

<!-- Formulaire de changement de mot de passe - envoie vers userController->changePwd() -->
<form method="POST" action="index.php?controller=user&action=changePwd">
    <h2>Modifier mon mot de passe</h2>

    <!-- Champ nouveau mot de passe -->
    <label for="new_password">Nouveau mot de passe :</label>
    <input type="password" id="new_password" name="new_password" required><br>

    <!-- Confirmation du mot de passe -->
    <label for="confirm_password">Confirmer mot de passe :</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br>

    <!-- Bouton de soumission -->
    <button type="submit" name="changePwd">Valider le changement</button>
</form>
