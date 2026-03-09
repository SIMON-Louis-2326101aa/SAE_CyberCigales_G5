<?php

?>
<div class="victory-container">
    <h1>Félicitations !</h1>
    <p>Vous avez réussi à trouver où est le coffre, il ne vous reste plus qu'à aller le déverrouiller.</p><br>
    <p>Vous avez resolu notre jeu en : <strong> <?php $seconds = $_SESSION['gameprogress']['total_time_sec'] ?? 0;
        echo htmlspecialchars(gmdate("H:i:s", $seconds)); ?></strong></p><br>
    <p>Merci d'avoir joué à Escape The Code. Nous espérons que vous avez apprécié l'aventure !</p><br>
    <a href="index.php?controller=Redirection&action=openHomepage" class="btn-nav">RETOUR À L'ACCUEIL</a><br>
</div>
