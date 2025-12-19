<form action="index.php?controller=EmailContact&action=sendContactEmail" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">

    <input type="email" name="email" placeholder="Votre email" required
           value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">

    <input type="text" name="sujet" placeholder="Sujet" required
           value="<?= htmlspecialchars($_SESSION['old']['sujet'] ?? '') ?>">

    <textarea name="message" required><?= htmlspecialchars($_SESSION['old']['message'] ?? '') ?></textarea>

    <button type="submit">Envoyer</button>
</form>