<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$info = "phishing";
$clue = "Indice 1 (5min): <span id=\"clue-text-1\" class=\"timed-clue\">
Le mail 3 semble provenir d'une source officielle.</span><br>";

require_once __DIR__ . "/../../includes/layout/headerClue.php";
$team = $_SESSION['team'] ?? 'alice';
/* Récupération de l'équipe et de l'état mémorisé en session pour la restauration après fausse réponse */
$state = $_SESSION['phishing_state'] ?? null;
$motherName = ($team === 'alice') ? 'Clara' : 'Diane';
?>

<div class="hero-container-welcome">
    <p>Le papillon vous mene sur votre PC</p>
    <p>Vous ouvrez la boîte mail de votre mère (<?php echo $motherName; ?>).</p>
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
                    <div class="mail-subject-preview">Découvrez votre arbre!</div>
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
        <p>Qui est cette personne ?</p>
        
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
