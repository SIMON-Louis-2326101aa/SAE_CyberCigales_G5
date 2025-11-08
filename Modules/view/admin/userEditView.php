<h1>Modifier l'utilisateur</h1>

<form id="userForm" method="POST" action="index.php?controller=Admin&action=editUser&id=
<?php echo htmlspecialchars($user['id']); ?>">
    <label for="nom">Nom:</label><br>
    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>"><br>

    <label for="prenom">Prénom:</label><br>
    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>"><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br><br>

    <button type="submit">Enregistrer la modification</button>
    <button type="button" class="delete-user-account-btn" data-user-id="<?php echo $user['id']; ?>">
        Supprimer le compte</button>
</form>