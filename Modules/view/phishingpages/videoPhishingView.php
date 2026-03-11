<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?controller=Redirection&action=openFormConnection");
    exit;
}
?>

<div class="video-site-container">
    <header class="video-site-header">
        <div class="video-site-logo">
            VideoCloud Share
        </div>
    </header>

    <div class="video-player-container">
        <div id="unlock-overlay" class="video-unlock-overlay">
            <p class="video-title-placeholder">Vidéo privée partagée avec vous</p>
            <div class="multimedia-action-container">
                <button id="unlock-video-btn" class="impots-btn">DÉBLOQUER LA VIDÉO</button>
            </div>
        </div>

        <div id="epilepsy-warning" class="video-warning-box hidden">
            <p>⚠️ ATTENTION : Cette vidéo contient des flashs lumineux pouvant affecter les personnes épileptiques ou sensibles.</p>
        </div>

        <video id="trap-video" class="video-hidden" width="100%" controls>
            <source src="assets/video/phishingCat.mp4" type="video/mp4">
            U i i A i
        </video>
    </div>

    <div class="video-info-section">
        <h1 class="video-main-title">Vidéo de famille - Urgent</h1>
        <div class="video-meta">12 vues • Il y a 2 heures</div>
        <div class="video-description-box">
            Cette vidéo a été partagée via un lien sécurisé. Veuillez débloquer le contenu pour lancer la lecture automatique.
        </div>
    </div>

    <div class="fb-divider"></div>

    <div class="return-container-visible">
        <a href="index.php?controller=Puzzle&action=phishingLinkClick&from_id=6" class="impots-return-link">
            RETOUR SUR LA BOITE MAIL
        </a>
    </div>
</div>
