<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?controller=Redirection&action=openFormConnection");
    exit;
}
?>
<div class="phishing-page-container-welcome">
    <h1 class="phishing-trap-title">Paiement Généalogie Direct</h1>
    <div class="phishing-trap-card">
        <p>Cette page est une simulation de phishing.</p>
    </div>
    
    <a href="index.php?controller=Puzzle&action=phishingLinkClick&from_id=4" class="btn-nav btn-phishing-return">RETOUR SUR LA BOITE MAIL</a>
</div>
