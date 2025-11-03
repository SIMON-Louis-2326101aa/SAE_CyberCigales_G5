<h1>Modifier l'utilisateur</h1>

<form method="POST" action="index.php?controller=admin&action=editUser&id=<?php echo htmlspecialchars($user['id']); ?>">
    <label for="nom">Nom:</label><br>
    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>"><br>

    <label for="prenom">Prénom:</label><br>
    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>"><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br><br>

    <input type="submit" value="Enregistrer">
</form>