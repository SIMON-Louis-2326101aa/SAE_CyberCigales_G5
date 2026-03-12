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

<div class="genealogie-container" data-team="<?php echo htmlspecialchars($team); ?>">
    <div class="genealogie-header">
        <h1 class="genealogie-title">Généalogie Direct</h1>
        <p class="genealogie-subtitle">Retrouvez vos racines en un clic</p>
    </div>

    <div id="gen-card-box" class="genealogie-card">
        <h3>Créer un compte Premium</h3>
        <p class="gen-intro-text">
            Pour accéder aux documents inédits de la famille Valmont, veuillez finaliser votre inscription (19,99€ / an).
        </p>

        <form id="gen-form" onsubmit="event.preventDefault();">
            <div class="genealogie-form-group">
                <label for="gen-nom">Nom complet</label>
                <input type="text" id="gen-nom" placeholder="Jean Valjean" required>
            </div>

            <div class="genealogie-form-group">
                <label for="gen-email">Adresse e-mail</label>
                <input type="email" id="gen-email" placeholder="nom@exemple.com" required>
            </div>

            <div class="genealogie-form-group">
                <label for="gen-card">Numéro de carte bancaire</label>
                <input type="text" id="gen-card" placeholder="XXXX XXXX XXXX XXXX" required>
            </div>

            <button type="button" id="gen-fill-btn" class="genealogie-btn-fill" onclick="autoFillGenealogie()">
                Remplir le formulaire</button>
            <button type="button" id="gen-submit-btn" class="genealogie-btn-submit hidden"
                    onclick="showWarningPhishingGenealogie()">Créer le compte</button>
        </form>
    </div>

    <div id="gen-warning-msg" class="genealogie-warning">
        <h2>⚠️ VIGILANCE : TENTATIVE DE PHISHING</h2>
        <p>
            Cette page simulait une offre alléchante pour vous inciter à divulguer vos
            <strong>coordonnées bancaires</strong>.
            C'est une technique courante de phishing liée à la curiosité (recherches généalogiques).
        </p>
        <p><strong>Points à vérifier systématiquement :</strong></p>
        <ul class="gen-warning-list">
            <li>L'URL du site : est-elle cohérente avec le service proposé ?</li>
            <li>Le cadenas dans la barre d'adresse : est-il présent ? (mais attention,
                cela ne garantit pas l'honnêteté du site).</li>
            <li>La demande de paiement immédiat pour un service "gratuit" ou une "découverte".</li>
        </ul>
        <p><em>Ne transmettez jamais vos codes de carte bancaire par un lien reçu par mail
                sans avoir vérifié la source.</em></p>
    </div>

    <div id="gen-return-btn" class="return-container">
        <a href="index.php?controller=Puzzle&action=phishingLinkClick&from_id=4" class="genealogie-return-link">
            RETOUR SUR LA BOÎTE MAIL
        </a>
    </div>
</div>
