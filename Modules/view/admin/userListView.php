<h1>Liste des utilisateurs</h1>

<table class="db-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Date d'inscription</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td>
                    <button type="button" onclick="window.location.href='index.php?controller=admin&action=editUser&id=<?php echo $user['id']; ?>'">Modifier</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Utilisateurs en attente de vérification</h2>

<?php if (empty($pendingUsers)) : ?>
    <p>Aucun utilisateur en attente de vérification.</p>
<?php else : ?>
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
        <?php foreach ($pendingUsers as $pendingUser) : ?>
            <tr>
                <td><?php echo htmlspecialchars($pendingUser['id']); ?></td>
                <td><?php echo htmlspecialchars($pendingUser['nom']); ?></td>
                <td><?php echo htmlspecialchars($pendingUser['prenom']); ?></td>
                <td><?php echo htmlspecialchars($pendingUser['email']); ?></td>
                <td><?php echo htmlspecialchars($pendingUser['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
