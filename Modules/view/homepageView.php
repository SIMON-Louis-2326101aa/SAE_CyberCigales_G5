<?php
// homepageView.php
$message = 'Page d\'accueil';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
</head>
<body>
<h1><?php echo $message; ?></h1>

<!-- Exemple de bouton qui envoie vers le contrôleur -->
<form action="" method="post">
    <button type="submit">Cliquer</button>
</form>
</body>
</html>