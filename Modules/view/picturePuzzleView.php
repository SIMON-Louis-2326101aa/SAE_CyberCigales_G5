<?php
if (!isset($_SESSION['enigme2_start'])) {
    $_SESSION['enigme2_start'] = time();
}

$start = $_SESSION['enigme2_start'];?>
<script>
    const ENIGME_START = <?= $start * 1000 ?>;
</script>
<?php
$info = "rien";
$clue = "
<ul class='clues'>

<li class='clue-block'>
    <div class='clue-header'>
        🔒 Indice 1 dans <span id='clue-time-1'>05:00</span>
    </div>
    <div id='clue-text-1' class='clue-text'>
        La photo tourne
    </div>
</li>
</ul>";

require_once __DIR__ . "/../../includes/layout/headerClue.php";

if (isset($_SESSION['team']) && ($_SESSION['team'] === "alice")) : ?>
    <div class="enigma-container">

        <div class="enigma-text">
            <p class="perso-speak">Cette lettre était donc adressée à ma mère… Mais pourquoi était-elle cryptée comme
                ça ?
                Et de quel trésor mes arrière-grandparents parlent.</p>
            <p class="perso-speak"> Oh on dirait des morceaux de photo au fond de l'enveloppe. </p>
        </div>

        <div class="photo-enigma">
            <div class="photo-card" id="photoCard">
                <div class="photo-face photo-front">
                    <div id="photoPuzzle" class="photo-puzzle"></div>
                </div>
                <div class="photo-face photo-back">
                    <p class="back-image">Le papillon garde la mémoire.</p>
                </div>
            </div>
        </div>

        <div class="enigma-text">
            <p class="perso-speak">Tiens, la photo est vraiment très floue et toute mélangée.
                <br>Je vais devoir la reconstituer avant de pouvoir tenter autre chose.</p>
        </div>

        <div class="enigma-form">
        <form method="POST" action="index.php?controller=Puzzle&action=validatePhoto">
            <label>
                Quoi retenir de cette phrase ? (2 mot attendu)<br>
                <textarea name="answer" required></textarea>
            </label>

            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>
    </div>

<?php elseif (isset($_SESSION['team']) && ($_SESSION['team'] === "bob")) : ?>
<div class="enigma-container">

    <div class="enigma-text">
        <p class="perso-speak">Cette lettre était donc adressée à ma mère… Mais pourquoi était-elle cryptée comme ça ?
            Et de quel trésor mes arrière-grandparents parlent.</p>
        <p class="perso-speak"> Oh on dirait des morceau de photo au fond de l'enveloppe. </p>
    </div>

<div class="photo-enigma">
    <div class="photo-card" id="photoCard">
        <div class="photo-face photo-front">
            <div id="photoPuzzle" class="photo-puzzle"></div>
        </div>
        <div class="photo-face photo-back">
            <p class="back-image">
                    La clé ouvre le passé.
            </p>
        </div>
    </div>
</div>
    <div class="enigma-text">
        <p class="perso-speak">Tiens, la photo est vraiment très floue et toute mélangée.
        <br>Je vais devoir la reconstituer avant de pouvoir tenter autre chose.</p>
    </div>

    <div class="enigma-form">
        <form method="POST" action="index.php?controller=Puzzle&action=validatePhoto">
            <label>
                Quoi retenir de cette phrase ? (2 mot attendu)<br>
                <textarea name="answer" required></textarea>
            </label>

            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>
</div>

<?php else : ?>
    <p>Erreur : Veuillez choisir une équipe ou vous reconnecter</p>
<br>
<?php endif; ?>
