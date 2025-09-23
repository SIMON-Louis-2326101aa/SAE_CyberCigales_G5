<?php
// homepageView.php
$title = 'Page d\'accueil';
$message = 'Bienvenue';
$messageInscription = 'Inscription';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
</head>
<body>
<h1><?php echo $title; ?></h1>
<h2><?php echo $message; ?></h2>
<a href="/Modules/view/inscription.php" class="btn">
    <button><?php echo $messageInscription ?></b></button>
</a>
</body>
</html>