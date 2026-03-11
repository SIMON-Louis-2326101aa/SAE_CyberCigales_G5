<?php

?>
<div class="victory-container">

    <h1>Félicitations !</h1>

    <p>
        Vous avez réussi à découvrir où se trouve le coffre.
        Il ne vous reste plus qu'à aller le déverrouiller !
    </p>
    <p class="victory-story">
        Les indices laissés par vos arrière-grands-parents vous ont menés jusqu’à la vérité.<br>
        Il ne vous reste plus qu’à vous rendre à l’endroit indiqué pour découvrir ce qu’ils ont laissé derrière eux…
    </p><br>
    <p class="victory-time">
        Temps de résolution :<strong> <?php $seconds = $_SESSION['gameprogress']['total_time_sec'] ?? 0;
            echo htmlspecialchars(gmdate("H:i:s", $seconds)); ?></strong>
    </p><br>
    <p>
        Merci d'avoir joué à <strong>Escape The Code</strong>.
        Nous espérons que vous avez apprécié l'aventure !
    </p><br>

    <div class="victory-buttons">
        <a href="index.php?controller=Redirection&action=openHomepage" class="btn-nav">
            Retour à l'accueil
        </a>
    </div><br>

</div>