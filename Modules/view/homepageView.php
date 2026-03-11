<?php // var_dump($_SESSION);
if (isset($_SESSION['user_id'])) : ?>
    <div class="hero-container-welcome">
        <h1>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?> !</h1>
        <br>
        <p><strong>Certains secrets ne se lisent pas, ils se déchiffrent.
                Derrière chaque symbole, chaque message ou ligne de code, se cache une vérité à découvrir.
                Entre cryptographie et cybersécurité, ton esprit est ta meilleure clé.
                Décrypte. Analyse. Protège.
            </strong></p>
        <p><strong>L’aventure commence ici.</strong></p>
    </div>

    <div class="hero-container-main">
        <h1 class="hero-question">Serez-vous capable de résoudre le mystère ?</h1>
        <p class="rules">
            Pour percer les mystères de ce jeu, vous devrez être <strong>deux joueurs</strong>.<br>
            Chacun choisira une <strong>équipe différente</strong> et suivra sa propre aventure.<br>
            ⚠️ <strong>Interdiction de regarder ce que fait votre partenaire</strong> : cela ne vous aidera pas.
        </p>
        <p class="rules">
            Pendant le jeu, plusieurs onglets vous aideront :
            <br>
            <strong>ℹ️ Informations</strong> : explications utiles pour comprendre certaines énigmes.<br>
            <strong>💡 Indices</strong> : aides débloquées au fur et à mesure du temps.<br>
            <strong>🎒 Inventaire</strong> : objets et éléments découverts pendant l’aventure.
        </p>
        <p class="rules">
            Les <strong>indices et informations apparaîtront progressivement</strong> : pensez à les consulter
            régulièrement.
            <br>
            Nous vous conseillons de prendre <strong>une feuille et un stylo</strong> pour noter vos découvertes.
        </p>
        <p class="rules">
            ⚠️ <strong>Aucune ressource externe n’est autorisée</strong>
            (pas d’internet, livres, etc.).<br>
            Toutes les solutions se trouvent <strong>dans le jeu</strong>.
        </p>
        <p class="rules">
            Les informations découvertes seront accessibles dans la <strong>page Informations</strong> au fur et à
            mesure de votre progression.
        </p>
        <a  href='index.php?controller=Team&action=play'
            class="active btn-nav">JOUER</a>
    </div>

<?php else : ?>
    <div class="hero-container-welcome">
        <h1>Bienvenue sur Escape The Code</h1>

        <p><strong>Certains secrets ne se lisent pas, ils se déchiffrent.
                Derrière chaque symbole, chaque message ou ligne de code, se cache une vérité à découvrir.
                Entre cryptographie et cybersécurité, ton esprit est ta meilleure clé.
                Décrypte. Analyse. Protège.
            </strong></p>
        <br>
        <p><strong>L’aventure commence ici.</strong></p>
    </div>

    <div class="hero-container-main">
        <h1 class="hero-question">Serez-vous capable de résoudre le mystère ?</h1>
        <p class="rules">Pour percer les mystères de ce jeu, il vous faudra être deux<br>
            Mais pas le droit de regarder ce que votre associé fait (ça ne vous aidera pas)<br>
            Vous choisirez chacun une équipe différente qui vous mènera ensemble au mystère de cette histoire</p>
        <a  href="index.php?controller=Redirection&action=openFormConnection"
            class="active btn-nav">SE CONNECTER POUR JOUER</a>
    </div>
<?php endif; ?>