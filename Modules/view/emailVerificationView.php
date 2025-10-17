<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification de l'email</title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
    <link rel="icon" href="/public/assets/images/favicon.ico">
</head>
<body>
<nav>
    <a href="index.php?controller=homepage&action=openHomepage" class="active">Home</a>
</nav>

<main>
    <h1>Vérifier votre email</h1>

    <?php if (!empty($info)): ?>
        <p><?php echo htmlspecialchars($info); ?></p>
    <?php endif; ?>
    <?php if (!empty($devCode)): ?>
        <p><strong>Code (dev/local):</strong> <?php echo htmlspecialchars($devCode); ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="index.php?controller=emailVerification&action=verify" method="post">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
        <label for="code">Code reçu par email</label>
        <input type="text" id="code" name="code" required maxlength="6" pattern="^[0-9]{6}$" inputmode="numeric" placeholder="123456">
        <button type="submit">Vérifier</button>
    </form>

    <p>
        <a href="index.php?controller=emailVerification&action=request&email=<?php echo urlencode($email ?? ''); ?>">Renvoyer un code</a>
    </p>
</main>

<footer>
    <a href="index.php?controller=legalMention&action=legal" class="active">Mentions Légales</a>
    |
    <a href="index.php?controller=sitemap&action=show" class="active">Plan du site</a>
    </footer>

</body>
</html>


