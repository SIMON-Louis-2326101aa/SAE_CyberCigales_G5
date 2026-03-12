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

<div class="colis-container" data-team="<?php echo htmlspecialchars($team); ?>">
    <div class="colis-header">
        <div class="colis-logo">La Poste</div>
    </div>

    <div id="colis-login-card" class="colis-card">
        <h3>Suivi de votre colis n°8L9452</h3>
        <p class="gen-intro-text">
            Connectez-vous à votre compte pour régulariser les frais d'envoi (1,99€) et programmer la livraison.
        </p>

        <form id="colis-form" onsubmit="event.preventDefault();">
            <div class="colis-form-group">
                <label for="colis-email">Identifiant ou adresse e-mail</label>
                <input type="text" id="colis-email" placeholder="nom@exemple.fr" required>
            </div>

            <div class="colis-form-group">
                <label for="colis-pass">Mot de passe</label>
                <input type="password" id="colis-pass" placeholder="••••••••" required>
            </div>

            <button type="button" id="colis-fill-btn" class="colis-btn-fill" onclick="autoFillColis()">Remplir les identifiants</button>
            <button type="button" id="colis-login-btn" class="colis-btn-submit hidden" onclick="showWarningPhishingColis()">Se connecter</button>
        </form>
    </div>

    <div id="colis-warning-msg" class="colis-warning">
        <h2>⚠️ ALERTE : PHISHING DÉTECTÉ</h2>
        <p>
            Ceci est une simulation de phishing très courante. Les pirates usurpent l'identité de <strong>La Poste</strong>
            pour voler vos identifiants et, dans l'étape suivante, vos coordonnées bancaires.
        </p>
        <p><strong>Indices qui auraient dû vous alerter :</strong></p>
        <ul class="gen-warning-list">
            <li><strong>L'urgence :</strong> Le message vous demande d'agir vite.</li>
            <li><strong>La demande d'argent :</strong> Un petit montant est demandé pour débloquer une situation crédible.</li>
            <li><strong>L'URL :</strong> Les sites officiels sont toujours en <strong>laposte.fr</strong>.</li>
        </ul>
        <p><em>Règle d'or : Ne cliquez jamais sur un lien de suivi de colis reçu par mail. Allez directement sur le site officiel et tapez votre numéro de suivi.</em></p>
    </div>

    <div id="colis-return-btn" class="return-container">
        <a href="index.php?controller=Puzzle&action=phishingLinkClick&from_id=5" class="colis-return-link">
            RETOUR SUR LA BOÎTE MAIL
        </a>
    </div>

    <div class="colis-footer">
        © 2026 La Poste - Tous droits réservés
    </div>
</div>
