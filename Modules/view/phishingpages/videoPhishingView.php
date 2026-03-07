<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['utilisateur'])) {
    header("Location: index.php?controller=Redirection&action=openFormConnection");
    exit;
}
?>

<div class="hero-container-welcome">
    <h1 class="phishing-trap-title">Contenu Multimédia</h1>
    <div class="phishing-trap-card">
        <p>Veuillez cliquer sur le bouton ci-dessous pour débloquer le contenu de la vidéo.</p>
        
        <div class="multimedia-action-container">
            <button id="unlock-video-btn" class="btn-nav">DÉBLOQUER LA VIDÉO</button>
        </div>

        <video id="trap-video" class="video-hidden" width="100%" controls>
            <source src="assets/video/phishingCat.mp4" type="video/mp4">
            U i i A i
        </video>
    </div>
    
    <a href="index.php?controller=Puzzle&action=phishingLinkClick&from_id=6" class="btn-nav btn-phishing-return">RETOUR SUR LA BOITE MAIL</a>
</div>
