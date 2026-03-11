<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?controller=Redirection&action=openFormConnection");
    exit;
}

// Récupération de l'équipe pour le remplissage automatique
$team = $_SESSION['team'] ?? 'alice';
?>

<div class="impots-container" data-team="<?php echo htmlspecialchars($team); ?>">
    <div class="impots-header">
        <div>
            <h2 class="impots-fr-header">République Française</h2>
            <small>Liberté • Égalité • Fraternité</small>
        </div>
        <div class="impots-domain">impots.gouv.fr</div>
    </div>
    
    <div class="impots-content">
        <div id="impots-intro">
            <h3>Formulaire de remboursement d'impôt</h3>
            
            <p>
                Après les derniers calculs de votre activité annuelle, nous avons déterminé que vous êtes admissible à recevoir un remboursement d'impôt de <strong>184.50 €</strong>.
                Veuillez remplir le formulaire ci-dessous pour confirmer vos informations.
            </p>
        </div>

        <form id="phishing-form" class="impots-form" onsubmit="event.preventDefault();">
            <div class="impots-form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom" placeholder="Mme/M. Jean Dupont" required>
            </div>
            
            <div class="impots-form-group">
                <label for="birth">Date de naissance</label>
                <input type="date" id="birth" name="birth" required>
            </div>

            <div class="impots-form-group">
                <label for="address">Adresse de résidence</label>
                <input type="text" id="address" name="address" placeholder="123 Rue de la Paix, 75000 Paris" required>
            </div>

            <div class="impots-form-group">
                <label for="tel">Numéro de téléphone</label>
                <input type="tel" id="tel" name="tel" placeholder="06 00 00 00 00" required>
            </div>

            <div class="impots-form-group">
                <label for="card">Numéro de carte bancaire (pour le crédit)</label>
                <input type="text" id="card" name="card" placeholder="XXXX XXXX XXXX XXXX" required>
            </div>

            <button type="button" id="fill-btn" class="impots-btn" onclick="autoFillImpots()">Remplir le formulaire (Via l'autocomplétion du navigateur)</button>
            <button type="button" id="submit-btn" class="impots-btn hidden" onclick="showWarningPhishingImpots()">Valider</button>
        </form>

        <div id="scammer-msg" class="scammer-message">
            <p>Merci l'idiot ! Tes coordonnées bancaires sont maintenant entre nos mains. $$$$$</p>
            <p>On va bien s'amuser avec ta thune...</p>
        </div>

        <div id="warning-msg" class="warning-message">
            <strong class="impots-warning-title">⚠️ ATTENTION !</strong><br><br>
            C'est exactement ce qui arrive lors d'un vol de données. L'arnaqueur se moque de vous dès qu'il a ce qu'il veut.
            <br><br>
            <strong>Conseils de sécurité :</strong>
            <ul class="impots-warning-list">
                <li>Vérifiez toujours l'URL dans la barre d'adresse. Les sites officiels de l'administration se terminent par <strong>.gouv.fr</strong>.</li>
                <li>L'administration ne vous demandera JAMAIS vos coordonnées bancaires par email ou SMS pour un remboursement.</li>
                <li>Ne saisissez jamais d'informations importantes si vous avez un doute sur l'origine du message.</li>
            </ul>
        </div>

        <div id="return-btn" class="return-container">
            <a href="index.php?controller=Puzzle&action=phishingLinkClick&from_id=1" class="impots-return-link">
                RETOUR SUR LA BOITE MAIL
            </a>
        </div>
    </div>
    
    <div class="impots-footer">
        © Direction générale des Finances publiques - Ministère de l'Économie, des Finances et de la Souveraineté industrielle et numérique
    </div>
</div>
