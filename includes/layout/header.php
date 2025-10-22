<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $A_params['pageTitle'] ?? 'CyberCigales'; ?></title>
    <link rel="stylesheet" href="/assets/styles/stylesheet.css">
    <link rel="icon" href="/assets/images/favicon.ico">
</head>
<body>
<nav class="nav-menu">

    <div id="nav-menu-sections">
        <a href="index.php?controller=homepage&action=openHomepage" class="active">Home</a>
    </div>

    <div id="nav-auth-buttons">
        <a href="index.php?controller=formRegister&action=register" class="active"><button>Inscription</button></a>
        <a href="index.php?controller=formConnection&action=login" class="active"><button>Connexion</button></a>
    </div>

</nav>
