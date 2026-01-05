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
                    <button type="button" class="edit-user-btn" data-user-id="<?php echo $user['id']; ?>">
                        Modifier</button>
                    <br>
                    <?php if ($user['is_banned'] == 0) : ?>
                    <a href="index.php?controller=Admin&action=banUser&id=<?= $user['id']; ?>" class="active btn-nav">
                            Bannir</a>
                    <?php else : ?>
                    <a href="index.php?controller=Admin&action=unbanUser&id=<?= $user['id']; ?>"
                            class="active btn-nav"> Débannir </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<br>
<h1>Utilisateurs en attente de vérification</h1>

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
                <th>Actions</th>
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
                    <td>
                        <button type="button" class="approve-pending-btn" data-pending-id="
                        <?php echo $pendingUser['id']; ?>">Approuver</button>
                        <button type="button" class="delete-pending-btn" data-pending-id="
                        <?php echo $pendingUser['id']; ?>">Supprimer</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<br>
<br>
<h1>Avancée des joueurs </h1>
<table class="db-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Id de l'utilisateur</th>
        <th>Equipe</th>
        <th>Niveau</th>
        <th>Date/heure de debut</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($progressGames as $gameProgress) : ?>
        <tr>
            <td><?php echo htmlspecialchars($gameProgress['id']); ?></td>
            <td><?php echo htmlspecialchars($gameProgress['user_id']); ?></td>
            <td><?php echo htmlspecialchars($gameProgress['team']); ?></td>
            <td><?php echo htmlspecialchars($gameProgress['level']); ?></td>
            <td><?php echo htmlspecialchars($gameProgress['game_start_time']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br>