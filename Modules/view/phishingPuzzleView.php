<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['enigme4_start'])) {
    $_SESSION['enigme4_start'] = time();
}

$start = $_SESSION['enigme4_start'];?>
<script>
    const ENIGME_START = <?= $start * 1000 ?>;
</script>
<?php
$info = "phishing";
$clue = "
<ul class='clues'>

<li class='clue-block'>
    <div class='clue-header'>
        🔒 Indice 1 <span id='clue-time-1'>05:00</span>
    </div>
    <div id='clue-text-1' class='clue-text'>
        Le mail 3 semble provenir d'une source officielle.
    </div>
</li>
</ul>";

require_once __DIR__ . "/../../includes/layout/headerClue.php";
$team = $_SESSION['team'] ?? 'alice';
/* Récupération de l'équipe et de l'état mémorisé en session pour la restauration après fausse réponse */
$state = $_SESSION['phishing_state'] ?? null;
$motherName = ($team === 'alice') ? 'Diane' : 'Clara';
?>

<div class="hero-container-welcome" id="phishing-container-welcome">
    <p>Le signal du papillon s’est enfin stabilisé sur le PC de votre mère (<?php echo $motherName; ?>).</p>
    <p>En ouvrant sa boîte mail, vous sentez que les archives numériques cachent encore un secret...</p>
    <p>Explorez ces messages pour retrouver ce que la mémoire a laissé de côté sur la photo de famille.</p>
</div>

<div class="phishing-area" id="phishing-container" 
     data-team="<?php echo $team; ?>"
     data-open-mail="<?php echo htmlspecialchars((string)($state['open_mail'] ?? '')); ?>"
     data-open-pdf="<?php echo htmlspecialchars((string)($state['open_pdf'] ?? '')); ?>">

    <div class="mail-header">
        <span class="mail-app-name">Boîte Mail</span>
    </div>

    <div class="mail-main-container">
        <div class="mail-sidebar">
            <ul class="email-list-simple">
                <li class="email-item-logic" data-id="1">
                    <div class="mail-sender-name">Impôts Gouv</div>
                    <div class="mail-subject-preview">Remboursement...</div>
                </li>
                <li class="email-item-logic" data-id="2">
                    <div class="mail-sender-name">Faceb00k Security</div>
                    <div class="mail-subject-preview">Alerte de sécurité</div>
                </li>
                <li class="email-item-logic" data-id="3">
                    <div class="mail-sender-name">Archives 92</div>
                    <div class="mail-subject-preview">Votre demande n°7845</div>
                </li>
                <li class="email-item-logic" data-id="4">
                    <div class="mail-sender-name">Généalogie Direct</div>
                    <div class="mail-subject-preview">Découvrez votre arbre !</div>
                </li>
                <li class="email-item-logic" data-id="5">
                    <div class="mail-sender-name">La Poste</div>
                    <div class="mail-subject-preview">Colis bloqué</div>
                </li>
                <li class="email-item-logic" data-id="6">
                    <div class="mail-sender-name">Video Cloud</div>
                    <div class="mail-subject-preview">Vidéo reçue</div>
                </li>
            </ul>
        </div>

        <div id="email-display-area" class="mail-content-display">
            <div class="mail-placeholder">
                <p>Sélectionnez un message pour le lire.</p>
            </div>
        </div>
    </div>

    <div id="pdf-simulation" class="pdf-simu">
    </div>

    <div id="validation-section" class="phishing-question hidden">
        <h3>Analyse du document</h3>
        <p>Qui est cette personne pour vous ? (lien familial)</p>
        
        <form action="index.php?controller=Puzzle&action=validatePhishing" method="POST" class="phishing-form">
            <input type="text" name="answer" placeholder="Votre réponse" required class="phishing-input"
                   value="<?php echo htmlspecialchars($state['answer'] ?? ''); ?>">
            <button type="submit" class="btn-nav phishing-submit">VALIDER</button>
        </form>
    </div>
</div>

<?php
/* Suppression de l'état après affichage pour ne pas interférer avec les futures tentatives */
unset($_SESSION['phishing_state']);
?>
