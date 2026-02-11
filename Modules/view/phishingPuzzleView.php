<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$team = $_SESSION['team'] ?? 'alice';
$targetTante = ($team === 'alice') ? 'Diane VALMONT' : 'Clara VALMONT';
?>

<div class="hero-container-welcome">
    <h1>La boîte mail</h1>
</div>

<div class="phishing-area">
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
                <li class="email-item-logic active" data-id="3">
                    <div class="mail-sender-name">Archives 92</div>
                    <div class="mail-subject-preview">Votre demande n°7845</div>
                </li>
            </ul>
        </div>

        <div id="email-display-area" class="mail-content-display">
            <div class="mail-detail-meta">
                <strong>De :</strong> archives.departementales@hauts-de-seine.fr<br>
                <strong>Objet :</strong> Votre demande d'acte n°7845
            </div>
            
            <div class="mail-message-body">
                <p>Bonjour,</p>
                <p>Veuillez trouver ci-joint la copie de l'acte de naissance demandé.</p>
                
                <div class="attachment-logic">
                    <div class="attachment-icon-simu"></div>
                    <div class="attachment-info">
                        <strong>acte_de_naissance_7845.pdf</strong>
                        <span class="attachment-action-text">Cliquer pour visualiser</span>
                    </div>
                </div>
                
                <p>Cordialement,<br>Le service des archives.</p>
            </div>
        </div>

    </div>

    <div id="pdf-simulation" class="pdf-simu show">
        <div class="pdf-header-border">
            <h2 class="pdf-title">EXTRAIT D'ACTE DE NAISSANCE</h2>
            <p class="pdf-subtitle">Commune de Boulogne-Billancourt</p>
        </div>

        <div class="pdf-body-content">
            <p>Le <strong>18 mars 1972</strong>, est née :</p>
            <h3 class="pdf-person-name"><?php echo $targetTante; ?></h3>
            
            <p>Fille de Pierre VALMONT et de Suzanne LECLERC.</p>

            <div class="pdf-handwritten">
                <span class="handwritten-label">Note manuscrite :</span><br>
                <?php if ($team === 'alice'): ?>
                    Coordonnées : <strong>43°14'18.6"N</strong>
                <?php else: ?>
                    Coordonnées : <strong>5°26'18.1"E</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="phishing-question">
        <h3>Analyse du document</h3>
        
        <form action="index.php?controller=Puzzle&action=validatePhishing" method="POST" class="phishing-form">
            <input type="text" name="answer" placeholder="Votre réponse" required class="phishing-input">
            <button type="submit" class="btn-nav phishing-submit">VALIDER</button>
        </form>
    </div>
</div>
