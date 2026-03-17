<?php
if (!isset($_SESSION['enigme6_start'])) {
    $_SESSION['enigme6_start'] = time();
}

$start = $_SESSION['enigme6_start'];?>
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
        Qui est cette autre enfant sur la photo de famille ?
    </div>
</li>
<li class='clue-block'>
    <div class='clue-header'>
        🔒 Solution <span id='clue-time-3'>15:00</span>
    </div>
    <div id='clue-text-3' class='clue-text'>
        C'est votre cousin !
    </div>
</li>
</ul>";
require_once __DIR__ . "/../../includes/layout/headerClue.php";

if (isset($_SESSION['team']) && $_SESSION['team'] === "alice") : ?>
    <br>
    <p class="perso-speak">Voyons voir tout ce qu'on a trouvé jusqu'ici pour voir si on découvre quelque chose de plus.
    </p><br>

    <div class="s-section">
        <p>Épreuve 1</p>
        <p class="perso-speak">On a eu la lettre décryptée :</p>
        <p>Ma chère Diane,
            Si tu lis ces lignes, c’est que le temps a poursuivi sa route sans nous attendre.
            Nous ne savons pas ce que la vie t’a réservé, mais nous espérons qu’elle t’a offert
            autant de raisons d’aimer que de pardonner.
            Il est des blessures que l’on tait trop longtemps, croyant qu’elles disparaîtront
            d’elles-mêmes. Mais le silence ne soigne pas, il endort seulement la douleur.
            Nous avons vu, dans le regard de ta fille Alice, cette même lueur que tu avais enfant :
            celle de la curiosité et du courage mêlés.
            Ne la laisse pas s’éteindre, même si le monde tente de la couvrir d’ombre.
            Tout ce que nous avons construit, tout ce que nous avons caché, nous l’avons fait
            pour que quelqu’un comme elle puisse un jour comprendre.
            Avec toute la tendresse que le vent n’a pas emportée.
            Tes grands-parents qui t'aiment.</p>
        <p class="perso-speak">et le mot en morse : </p>
            <p>REFLET</p>
    </div>

    <div class="s-section">
        <p>Épreuve 2</p>
        <p class="perso-speak">Puis nous avons trouvé la photo de famille en morceaux et les deux mots clés</p>
        <div class="photo-container">
            <img src="./assets/images/photoFamille.png" alt="photodefamille">
            <p class="perso-speak">Tiens, la photo n'est plus floue.
                <br>Oh mais c'est moi petite à gauche ! Avec maman à côté de moi !
                <br>Mais qui sont ces autres personnes ?</p>
        </div>
        <p>PAPILLON et MÉMOIRE</p>
    </div>

    <div class="s-section">
        <p>Épreuve 3</p>
        <p class="perso-speak">Ici, on a suivi le signal déguisé en papillon qui nous a mené à ADMIN
        <br>Ainsi qu'à un message étrange : Bravo Oscar Bravo</p>
    </div>

    <div class="s-section">
        <p>Épreuve 4</p>
        <p class="perso-speak">On a trouvé le certificat de naissance de notre tante.
            On a découvert l'existence d'une tante !</p>
        <div id="pdf-simulation-summary" class="pdf-simu show" style="border: 2px solid #333;">
            <div class="pdf-header-border">
                <h2 class="pdf-title">EXTRAIT D'ACTE DE NAISSANCE</h2>
                <p class="pdf-subtitle" style="color: #333;">Commune de Boulogne-Billancourt</p>
            </div>
            <div class="pdf-body-content">
                <p style="color: #333;">Le <strong>18 mars 1978</strong>, est née :</p>
                <h3 class="pdf-person-name">Clara VALMONT</h3>
                <p style="color: #333;">Fille de Pierre VALMONT et de Suzanne LECLERC. Sœur de Diane VALMONT.</p>
                <div class="pdf-handwritten">
                    <span class="handwritten-label">Note manuscrite :</span>
                    <strong>D</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="s-section">
        <p>Épreuve 5</p>
        <p class="perso-speak">On a recréé un mot de passe plus robuste</p>
    </div>

    <form action="index.php?controller=Puzzle&action=valideSummary" method="post">
        <label>Qu'est-ce qu'on apprend de plus ?</label>
        <input type="text" name="answer" placeholder="Entrez votre réponse ">
        <button type="submit">Soumettre</button>
    </form>

<?php elseif (isset($_SESSION['team']) && $_SESSION['team'] === "bob") : ?>
    <br>
    <p class="perso-speak">Voyons voir tout ce qu'on a trouvé jusqu'ici pour voir si on en trouve plus </p><br>

    <div class="s-section">
        <p>Épreuve 1</p>
        <p class="perso-speak">On a eu la lettre décryptée :</p>
        <p>Ma chère Clara,
        Le temps nous a glissé entre les doigts comme du sable, mais avant qu’il ne disparaisse
        complètement, nous voulions te confier ces mots.
        Il n’y a pas de faute trop ancienne pour être pardonnée, ni de distance trop grande
        pour être franchie.
        Parfois, la vie nous sépare non pour nous punir, mais pour nous apprendre à revenir.
        Ton fils Bob possède déjà cette flamme que nous avons reconnue : la soif de comprendre,
        d’aller au-delà des évidences.
        Aide-le à écouter ce qu’on ne dit pas, à lire ce qu’on ne montre plus.
        Ce que nous avons laissé derrière nous n’est pas un trésor d’or ou de pierre, mais un
        message, une part de notre histoire, cachée dans les plis du temps.
        Avec l’espoir que les chemins perdus se croisent à nouveau.
        Tes grands-parents qui t'aiment.</p>
        <p class="perso-speak">et le mot en morse : </p><p>REFLET </p>
    </div>

    <div class="s-section">
        <p>Épreuve 2</p>
        <p class="perso-speak">Puis nous avons trouvé la photo de famille en morceaux et les deux mots clés</p>
        <div class="photo-container">
            <img src="./assets/images/photoFamille.png" alt="photodefamille">
            <p class="perso-speak">Tiens, la photo n'est plus floue.
                <br>Oh mais c'est moi petit à droite ! Avec maman à côté de moi !
                <br>Mais qui sont ces autres personnes ?</p>
        </div>
        <p>PASSÉ et CLÉ</p>
    </div>

    <div class="s-section">
        <p>Épreuve 3</p>
        <p class="perso-speak">Ici, on a suivi le signal déguisé en papillon qui nous a mené à ROOT
        <br>Ainsi qu'à un message étrange : Alpha Lima India Charlie Echo</p>
    </div>

    <div class="s-section">
        <p>Épreuve 4</p>
        <p class="perso-speak">On a trouvé le certificat de naissance de notre tante.
            On a découvert l'existence d'une tante !</p>
        <div id="pdf-simulation-summary" class="pdf-simu show" style="border: 2px solid #333;">
            <div class="pdf-header-border">
                <h2 class="pdf-title">EXTRAIT D'ACTE DE NAISSANCE</h2>
                <p class="pdf-subtitle" style="color: #333;">Commune de Boulogne-Billancourt</p>
            </div>
            <div class="pdf-body-content">
                <p style="color: #333;">Le <strong>18 mars 1978</strong>, est née :</p>
                <h3 class="pdf-person-name">Diane VALMONT</h3>
                <p style="color: #333;">Fille de Pierre VALMONT et de Suzanne LECLERC. Sœur de Clara VALMONT.</p>
                <div class="pdf-handwritten">
                    <span class="handwritten-label">Note manuscrite :</span>
                    <strong>9</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="s-section">
        <p>Épreuve 5</p>
        <p class="perso-speak">On a recréé un mot de passe plus robuste</p>
    </div>

    <form action="index.php?controller=Puzzle&action=valideSummary" method="post">
        <label>Qu'est-ce qu'on apprend de plus ?</label>
        <input type="text" name="answer" placeholder="Entrez votre réponse ">
        <button type="submit">Soumettre</button>
    </form>

<?php else : ?>
    <p>Erreur : veuillez choisir une équipe ou vous reconnecter.</p>
<?php endif; ?>