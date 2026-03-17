<?php
if (!isset($_SESSION['enigme8_start'])) {
    $_SESSION['enigme8_start'] = time();
}

$start = $_SESSION['enigme8_start'];?>
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
        Quel autre mot avont nous retenu avec CLE et MEMOIRE ? 
    </div>
</li>
</ul>";

require_once __DIR__ . "/../../includes/layout/headerClue.php";
?>
<br>
<?php if ($_SESSION["team"] === "alice") : ?>
    <p>Vous allez pouvoir rencontrez votre cousin (l'autre joueure(use) avec qui vous jouez) </p>
<?php else : ?>
    <p>Vous allez pouvoir rencontrez votre cousine (l'autre joueure(use) avec qui vous jouez) </p>
<?php endif; ?>
<p>Rassembler vos information pour voir vers ou ca peut vous mener </p>
<br>
<div class="s-section">
    <form method="POST" action="index.php?controller=Puzzle&action=valideMotCle">
        <p>Entrez les deux mots du <strong>passé</strong> pour déverrouiller la <strong>mémoire</strong>.</p>
        <label>Mots-clés : <br>
            <textarea name="answer1" required placeholder="Ex: mot1 et mot2"></textarea>
        </label>
        <button type="submit" class="active btn-nav">Déverrouiller le coffre</button>
    </form>
</div>
