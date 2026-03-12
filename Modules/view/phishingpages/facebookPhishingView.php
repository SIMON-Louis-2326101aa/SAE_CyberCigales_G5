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

<div class="facebook-container" data-team="<?php echo htmlspecialchars($team); ?>">
    <div class="facebook-logo">facebook</div>
    
    <div id="fb-login-card" class="facebook-login-card">
        <form class="facebook-form" onsubmit="event.preventDefault();">
            <input type="text" id="fb-email" placeholder="Adresse e-mail ou numéro de tél." required>
            <input type="password" id="fb-pass" placeholder="Mot de passe" required>
            
            <button type="button" id="fb-fill-btn" class="facebook-btn-fill" onclick="autoFillFacebook()">Remplir les identifiants</button>
            <button type="button" id="fb-login-btn" class="facebook-btn-login hidden" onclick="showWarningPhishingFacebook()">Se connecter</button>
            
            <div class="fb-forgot-pass">Mot de passe oublié ?</div>
            <hr class="fb-divider">
            <button type="button" class="fb-btn-create">Créer nouveau compte</button>
        </form>
    </div>

    <div id="fb-warning-msg" class="facebook-warning">
        <h2>⚠️ ALERTE DE SÉCURITÉ : PHISHING</h2>
        <p>
            Vous venez de simuler la saisie de vos identifiants sur une <strong>copie frauduleuse</strong> de Facebook. 
            Dans une situation réelle, vos accès (e-mail et mot de passe) auraient été volés instantanément.
        </p>
        <p><strong>Dangers spécifiques aux réseaux sociaux :</strong></p>
        <ul>
            <li><strong>Usurpation d'identité :</strong> Le pirate peut contacter
                vos amis en votre nom pour les arnaquer.</li>
            <li><strong>Accès à vos données privées :</strong> Photos,
                messages personnels et informations de contact sont exposés.</li>
            <li><strong>Revente de compte :</strong> Votre compte
                peut être utilisé pour diffuser des publicités frauduleuses ou du spam.</li>
        </ul>
        <p><em>Conseil : Activez toujours l'authentification à deux facteurs (2FA) pour protéger vos comptes.</em></p>
    </div>

    <?php
    $from = $_GET['from'] ?? 'email';
    $returnUrl = match ($from) {
        'instagram' => 'index.php?controller=Redirection&action=openSearchSM',
        default     => 'index.php?controller=Puzzle&action=phishingLinkClick&from_id=6',
    };
    ?>
    <div class="return-container-visible">
        <a href="<?= htmlspecialchars($returnUrl) ?>" class="impots-return-link">
            RETOUR
        </a>
    </div>
</div>
