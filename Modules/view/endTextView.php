<?php
if (!isset($_SESSION['enigme9_start'])) {
    $_SESSION['enigme9_start'] = time();
}

$start = $_SESSION['enigme9_start'];?>
<script>
    const ENIGME_START = <?= $start * 1000 ?>;
</script>
<?php
$info = "rien";
$clue = " 
<ul class='clues'>

<li class='clue-block'>
    <div class='clue-header'>
        🔒 Indice 1 <span id='clue-time-1'>05:00</span>
    </div>
    <div id='clue-text-1' class='clue-text'>
        ou nous fetions la vie -> regardes bien la photo 
        premier papier de cette vie -> regarde les certificats de naissance
        jour si special -> regarde les dates sur les deux
    </div>
</li>
</ul>";

require_once __DIR__ . "/../../includes/layout/headerClue.php";
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
        <textarea name="answer1" required></textarea><br>
    </label>
<br>
    <label>
        Quelle est le code du coffre?<br>
        <textarea name="answer2" required></textarea><br>
    </label>

    <button type="submit" class="btn-submit">Valider</button>
</form>
