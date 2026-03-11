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
        <p class="rules">Pour percer les mystères de ce jeu, il vous faudra être deux<br>
            Mais pas le droit de regarder ce que votre associé fait (ça ne vous aidera pas)<br>
            Vous choisirez chacun une équipe différente qui vous mènera ensemble au mystère de cette histoire</p>

        <p class="rules">Des petits onglets d'information (ℹ️), d'indice (💡) et un inventaire(🎒) seront là tout au long
            de votre jeu,<br>
            pour vous aider à avancer dans votre aventure, et vous informer sur les éléments plus complexes du jeu.<br>
            Les informations et indices vous arriveront au fur et à mesure du temps, il sera de votre devoir de penser à
            les regarder.
            <br>Nous vous conseillons de vous munir d'une feuille et d'un stylo, cela pourrait vous être très utile pour
            résoudre certaines énigmes du jeu.
            <br>Il vous est interdit de vous aider de ressources externes
            (internet, livres, etc.) pour résoudre les énigmes, tout doit être fait à l'aide de votre esprit
            et des éléments fournis dans le jeu.
            <br>Vous pourrez retrouver les informations que nous vous donnons dans la page du même nom qui arrive au
            vur et a mesure de vos decouverte. </p>
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