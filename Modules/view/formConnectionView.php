<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title> Connexion </title>
    <link rel="icon" href="/public/assets/images/favicon.ico">
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
</head>

<nav>
    <a href="index.php?controller=homepage&action=openHomepage" class="active">Home</a>
</nav>

<body>
<h1> Connection </h1>

<?php if (isset($_GET['registered']) && $_GET['registered'] === 'success'): ?>
    <div style="background-color: #e8f5e9; border: 2px solid #4caf50; padding: 15px; border-radius: 5px; margin: 15px 0;">
        <p style="color: #2e7d32; font-weight: bold; margin: 0;">
            🎉 Inscription réussie<?php if (isset($prenom) && isset($nom)): ?>, <?php echo htmlspecialchars($prenom . ' ' . $nom); ?><?php endif; ?> ! 
            Votre compte a été créé et votre email a été vérifié. Vous pouvez maintenant vous connecter.
        </p>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div style="background-color: #ffebee; border: 1px solid #f44336; padding: 15px; border-radius: 5px; margin: 15px 0;">
        <p style="color: #c62828; font-weight: bold; margin: 0;">❌ <?php echo htmlspecialchars($error); ?></p>
    </div>
<?php endif; ?>

<form action="index.php?controller=formConnection&action=login"  method="post" name="loginForm" autocomplete="on">
    E-mail : <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" autocomplete="email" required> <br>
    Mot de passe : <input type="password" name="pwd" autocomplete="current-password" required><br>
    <button type="submit" name="login">Se connecter</button>
    <a href="index.php?controller=forgotPwd&action=forgot" class="active">Mot de passe oublié?</a>
</form>
<a href="index.php?controller=formRegister&action=register" class="active">Pas encore inscrit ?</a>
<footer>
    <a href="index.php?controller=legalMention&action=legal" class="active">Mentions Légales</a>
</footer>
</body>
</html>
