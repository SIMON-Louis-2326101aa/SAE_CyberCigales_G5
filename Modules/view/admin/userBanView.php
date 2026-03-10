<h1>Bannir l'utilisateur</h1>

<form method="POST" action="index.php?controller=Admin&action=banUser&id=<?= $user['id']; ?>">

    <label for="nom">Nom:<?php echo htmlspecialchars($user['nom']); ?></label><br>

    <label for="prenom">Prénom:<?php echo htmlspecialchars($user['prenom']); ?></label><br>

    <label for="email">Email:<?php echo htmlspecialchars($user['email']); ?></label><br>

    <label for="reason">Raison du bannissement :</label>
    <textarea name="reason" id="reason" required></textarea>
    <br><br>

    <button type="submit" class="ban-user-btn">Confirmer le bannissement</button>
</form>