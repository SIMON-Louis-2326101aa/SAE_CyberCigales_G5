<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$team = $_SESSION['team'] ?? 'alice';
?>

<div class="hero-container-welcome">
    <h1>La boîte mail</h1>
</div>

<div class="phishing-area" id="phishing-container" data-team="<?php echo $team; ?>">
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
        
        <form action="index.php?controller=Puzzle&action=validatePhishing" method="POST" class="phishing-form">
            <input type="text" name="answer" placeholder="Votre réponse" required class="phishing-input">
            <button type="submit" class="btn-nav phishing-submit">VALIDER</button>
        </form>
    </div>
</div>
