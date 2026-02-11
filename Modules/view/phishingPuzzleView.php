<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$team = $_SESSION['team'] ?? 'alice';
?>

<div class="hero-container-welcome">
    <h1>Le faux mail</h1>
</div>

<div class="phishing-base-container">
    <section class="email-selection">
        <h2>Courriels reçus</h2>
        <ul>
            <li><strong>Email 1 :</strong> service-client@inf0-impots.gouv.fr - Remboursement</li>
            <li><strong>Email 2 :</strong> secure-check@faceb00k.security.com - Alerte sécurité</li>
            <li><strong>Email 3 :</strong> archives.departementales@hauts-de-seine.fr - Votre demande d'acte n°7845</li>
        </ul>
    </section>

    <hr>

    <section class="document-content">
        <h2>Document : Acte de naissance (Extrait)</h2>
        <p><strong>Nom :</strong> <?php echo ($team === 'alice') ? 'VALMONT Diane' : 'VALMONT Clara'; ?></p>
        <p><strong>Date de naissance :</strong> 18 mars 1972</p>
        <p><strong>Lieu :</strong> Boulogne-Billancourt</p>
        <p><strong>Parents :</strong> Pierre VALMONT et Suzanne LECLERC</p>
        <p><em>Note manuscrite en haut du document :</em></p>
        <p class="coordinates">
            <?php if ($team === 'alice'): ?>
                <strong>43°14'18.6"N</strong>
            <?php else: ?>
                <strong>5°26'18.1"E</strong>
            <?php endif; ?>
        </p>
    </section>

    <hr>

    <section class="puzzle-question">
        <h3>Qui est cette personne pour vous ?</h3>
        
        <form action="index.php?controller=Puzzle&action=validatePhishing" method="POST">
            <input type="text" name="answer" placeholder="Votre réponse..." required>
            <button type="submit" class="btn-nav">Valider</button>
        </form>
    </section>
</div>