<div class="titre" id="titre">
    <h4><strong>Escape the code</strong></h4>
    <p><strong>DANS LES PROFONDEURS DE LA JUNGLE TROPICALE, LES ANCIENS TEMPLES MAYAS GARDENT JALOUSEMENT LEURS SECRETS
            MILLÉNAIRES. CES SANCTUAIRES SACRÉS, CONSTRUITS PAR UNE CIVILISATION AUX CONNAISSANCES ASTRONOMIQUES ET
            MATHÉMATIQUES EXTRAORDINAIRES, RECÈLENT DES ÉNIGMES QUI DÉFIENT ENCORE AUJOURD'HUI LES PLUS BRILLANTS
            ESPRITS.</strong></p>
    <br>
    <p><strong>LA LÉGENDE RACONTE QU'UN TRÉSOR INESTIMABLE ATTEND CELUI QUI SAURA DÉCHIFFRER LES SIX ÉPREUVES SACRÉES
            LAISSÉES PAR LES GRANDS PRÊTRES MAYAS. CHAQUE TEMPLE CONTIENT UNE PART DU MYSTÈRE, CHAQUE ÉPREUVE RÉVÈLE
            UN FRAGMENT DE LA VÉRITÉ ANCESTRALE.</strong></p>
</div>

<?php if (isset($_SESSION['user_id'])) : ?>
    <div class="hero-container-main">
        <h1>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?> !</h1>
        <p>Vous êtes connecté(e) avec l'email : <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <br>
        <br>
        <h1 class="hero-question">Serez-vous capable de résoudre le mystère ?</h1>
        <br>
        <a  href='index.php?controller=Redirection&action=openLetterIntro'
            class="active btn-nav">JOUER</a>
    </div>

<?php else : ?>
    <div class="hero-container-main">
        <h1 class="hero-question">Serez-vous capable de résoudre le mystère ?</h1>
        <a  href="index.php?controller=Redirection&action=openFormConnection"
            class="active btn-nav">SE CONNECTER POUR JOUER</a>
    </div>
<?php endif; ?>