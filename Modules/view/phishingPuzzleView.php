<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$info = " <section class=\"phishing-info\">

    <h2>Qu’est-ce que le phishing ?</h2>

    <p>
        Le <strong>phishing</strong>, aussi appelé <strong>hameçonnage</strong>, est une technique utilisée par des 
        cybercriminels pour tromper les utilisateurs et leur voler des informations personnelles. 
        Les attaquants se font passer pour des organismes de confiance comme une banque, un service de paiement, 
        un réseau social ou une administration.
    </p>

    <p>
        Le plus souvent, le phishing se présente sous la forme d’un <strong>email, d’un SMS ou d’un message</strong> 
        contenant un lien vers un faux site internet qui imite parfaitement un site officiel. 
        Si la victime saisit ses informations (mot de passe, numéro de carte bancaire, données personnelles), 
        celles-ci sont récupérées par les fraudeurs.
    </p>

    <h2>Comment reconnaître un email de phishing ?</h2>

    <p>Certains signes doivent vous alerter :</p>

    <ul>
        <li>Une <strong>adresse email suspecte</strong> ou qui ne correspond pas à l’organisme officiel.</li>
        <li>Des <strong>fautes d’orthographe ou de grammaire</strong> dans le message.</li>
        <li>Un message <strong>urgent ou alarmant</strong> qui vous pousse à agir rapidement.</li>
        <li>Un <strong>lien étrange</strong> qui ne correspond pas au site officiel.</li>
        <li>Une demande d’<strong>informations sensibles</strong> 
        (mot de passe, numéro de carte bancaire, identifiants).</li>
    </ul>

    <h2>Comment se protéger du phishing ?</h2>

    <ul>
        <li><strong>Ne cliquez jamais directement</strong> sur un lien suspect dans un email ou un message.</li>
        <li><strong>Vérifiez l’adresse de l’expéditeur</strong> et l’URL du site avant de saisir des informations.</li>
        <li><strong>Accédez aux sites importants en tapant l’adresse vous-même</strong> dans votre navigateur.</li>
        <li>Activez la <strong>double authentification</strong> lorsque c’est possible.</li>
        <li>Utilisez des <strong>mots de passe différents et complexes</strong> pour chaque service.</li>
        <li>En cas de doute, <strong>contactez directement l’organisme concerné</strong>.</li>
    </ul>

    <h2>Que faire si vous recevez un message suspect ?</h2>

    <p>
        Si vous pensez avoir reçu un message de phishing, ne cliquez sur aucun lien et ne téléchargez aucune pièce 
        jointe.
        Vous pouvez également le signaler sur la plateforme officielle 
        <strong>www.internet-signalement.gouv.fr</strong>.
    </p>

    <p>
        Rester vigilant est la meilleure protection contre les tentatives d’hameçonnage.
    </p>

</section>";
$clue = "Indice 1 (5min): <span id=\"clue-text-1\" class=\"timed-clue\">
Le mail 3 semble provenir d'une source officielle.</span><br>";

require_once __DIR__ . "./../../includes/layout/headerClue.php";
$team = $_SESSION['team'] ?? 'alice';
/* Récupération de l'équipe et de l'état mémorisé en session pour la restauration après fausse réponse */
$state = $_SESSION['phishing_state'] ?? null;
$motherName = ($team === 'alice') ? 'Clara' : 'Diane';
?>

<div class="hero-container-welcome">
    <p>Le papillon vous mene sur votre PC</p>
    <p>Vous ouvrez la boîte mail de votre mère (<?php echo $motherName; ?>).</p>
</div>

<div class="phishing-area" id="phishing-container" 
     data-team="<?php echo $team; ?>"
     data-open-mail="<?php echo htmlspecialchars((string)($state['open_mail'] ?? '')); ?>"
     data-open-pdf="<?php echo htmlspecialchars((string)($state['open_pdf'] ?? '')); ?>">
    
    <div class="mail-header">
        <span class="mail-app-name">Boîte Mail</span>
    </div>
    
    <div class="mail-main-container">
        <div class="mail-sidebar">
            <ul class="email-list-simple">
                <li class="email-item-logic" data-id="1">
                    <div class="mail-sender-name">Impôts Gouv</div>
                    <div class="mail-subject-preview">Remboursement...</div>
                </li>
                <li class="email-item-logic" data-id="2">
                    <div class="mail-sender-name">Faceb00k Security</div>
                    <div class="mail-subject-preview">Alerte de sécurité</div>
                </li>
                <li class="email-item-logic" data-id="3">
                    <div class="mail-sender-name">Archives 92</div>
                    <div class="mail-subject-preview">Votre demande n°7845</div>
                </li>
                <li class="email-item-logic" data-id="4">
                    <div class="mail-sender-name">Généalogie Direct</div>
                    <div class="mail-subject-preview">Découvrez votre arbre!</div>
                </li>
            </ul>
        </div>

        <div id="email-display-area" class="mail-content-display">
            <div class="mail-placeholder">
                <p>Sélectionnez un message pour le lire.</p>
            </div>
        </div>
    </div>

    <div id="pdf-simulation" class="pdf-simu">
    </div>

    <div id="validation-section" class="phishing-question hidden">
        <h3>Analyse du document</h3>
        <p>Qui est cette personne ?</p>
        
        <form action="index.php?controller=Puzzle&action=validatePhishing" method="POST" class="phishing-form">
            <input type="text" name="answer" placeholder="Votre réponse" required class="phishing-input"
                   value="<?php echo htmlspecialchars($state['answer'] ?? ''); ?>">
            <button type="submit" class="btn-nav phishing-submit">VALIDER</button>
        </form>
    </div>
</div>

<?php
/* Suppression de l'état après affichage pour ne pas interférer avec les futures tentatives */
unset($_SESSION['phishing_state']);
?>
