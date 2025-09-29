<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $mdp = $_POST['mdp'];
    $confirm_mdp = $_POST['confirm_mdp'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "L’adresse e-mail n’est pas valide.";
        exit;
    }

    if($mdp != $confirm_mdp){
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    echo "<h3>Inscription réussie !</h3>";
    echo "Bienvenue, $prenom $nom";
    echo "Email : $email";
}
?>

