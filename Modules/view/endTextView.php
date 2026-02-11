<?php

?>
<div class="end">
    <p>Nous y sommes presque ils nous reste plus qu'a comprendre où se trouve le trésor !</p>
    <p>Il nous est dit : </p>
    <p>“Vous retrouverez nos souvenir la ou nous fêtions la vie, vous le trouverez sur le premier papier de cette vie,
    la clef est dans ce jour si spécial ”</p>

<img src="./assets/images/maps.png" alt="indice pour trouver le coffre" class="end-image">
</div>

<form action="index.php?controller=Puzzle&action=validateEnd" method="post" id="end-form">
    <label>
        Où allez ?<br>
        <textarea name="answer" required></textarea>
    </label>

    <button type="submit" class="btn-submit">Valider</button>
</form>
