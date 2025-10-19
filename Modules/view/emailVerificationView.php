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
        <div style="background-color: #e3f2fd; border: 1px solid #2196f3; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p style="color: #1565c0; font-weight: bold; margin: 0;">ℹ️ <?php echo htmlspecialchars($info); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($devCode)): ?>
        <div style="background-color: #fff3e0; border: 2px solid #ff9800; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p style="color: #e65100; font-weight: bold; margin: 0;">🔑 <strong>Code (dev/local):</strong> <?php echo htmlspecialchars($devCode); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div style="background-color: #ffebee; border: 1px solid #f44336; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p style="color: #c62828; font-weight: bold; margin: 0;">❌ <?php echo $error; ?></p>
        </div>
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


