<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\Modules\model\GameProgressModel;
use SAE_CyberCigales_G5\includes\ViewHandler;

class PuzzleController
{
    /**
     * Normalise un texte pour comparaison fiable
     */
    private function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Enigme 1 - Lettre et Morse
     */
    public function validateLetter()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        //RÉCUPÉRATION RÉPONSES
        $answerLetter = $_POST['answer1'] ?? '';
        $answerMorse  = $_POST['answer2'] ?? '';
        $_SESSION['old_answer1'] = $answerLetter;
        $_SESSION['old_answer2'] = $answerMorse;

        if (trim($answerLetter) === '' || trim($answerMorse) === '') {
            $_SESSION['flash_error'] = "Les deux réponses doivent être remplies.";
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        $team = $_SESSION['team'];

        //SOLUTIONS
        $letterSolutions = [
            'alice' => "
                Ma chère Diane,
                Si tu lis ces lignes, c’est que le temps a poursuivi sa route sans nous attendre.
                Nous ne savons pas ce que la vie t’a réservé, mais nous espérons qu’elle t’a offert
                autant de raisons d’aimer que de pardonner.
            ",

            'bob' => "
                Ma chère Clara,
                Le temps nous a glissé entre les doigts comme du sable, mais avant qu’il ne disparaisse
                complètement, nous voulions te confier ces mots.
                Il n’y a pas de faute trop ancienne pour être pardonnée, ni de distance trop grande
                pour être franchie.
            "
        ];

        $morseSolutions = [
            'alice' => 'reflet',
            'bob'   => 'reflet'
        ];

        if (!isset($letterSolutions[$team], $morseSolutions[$team])) {
            $_SESSION['flash_error'] = "Équipe inconnue.";
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // VALIDATION CÉSAR
        $answerLetterNorm = $this->normalize($answerLetter);
        $expectedLetter   = $this->normalize($letterSolutions[$team]);

        if ($answerLetterNorm !== $expectedLetter) {
            $_SESSION['flash_error'] = "La lettre n’a pas été correctement déchiffrée.";
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // VALIDATION MORSE
        $answerMorseNorm = $this->normalize($answerMorse);
        $expectedMorse   = $this->normalize($morseSolutions[$team]);

        if ($answerMorseNorm !== $expectedMorse) {
            $_SESSION['flash_error'] = "Le message codé est incorrect.";
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // SUCCÈS ON NETTOIE LES VARIABLES DE SAUVEGARDE ET ON CONTINUE
        unset($_SESSION['old_answer1'], $_SESSION['old_answer2']);

        $userId = $_SESSION['utilisateur']['id'];

        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 2);

        $_SESSION['flash_success'] = "Bravo ! Les deux messages ont été correctement déchiffrés.";

        header("Location: index.php?controller=Redirection&action=openPicturePuzzle");
        exit;
    }

    /**
     * Enigme 2 - Photo de famille et mots clés
     */
    public function validatePhoto()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            header("Location: index.php");
            exit;
        }

        $answer = strtolower(trim($_POST['answer'] ?? ''));
        if ($answer === '') {
            $_SESSION['flash_error'] = "Réponse vide.";
            header("Location: index.php?controller=Redirection&action=openPicturePuzzle");
            exit;
        }

        $solutions = [
            'alice' => ['mémoire', 'papillon'],
            'bob'   => ['passé', 'clé']
        ];

        $team = $_SESSION['team'];
        $isValid = false;

        foreach ($solutions[$team] as $word) {
            if (str_contains($answer, $word)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            $_SESSION['flash_error'] = "Ce n’est pas la bonne interprétation.";
            header("Location: index.php?controller=Redirection&action=openPicturePuzzle");
            exit;
        }

        // Niveau suivant
        $userId = $_SESSION['utilisateur']['id'];
        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 3);

        $_SESSION['flash_success'] = "Bien joué, tu avances dans l’enquête.";

        header("Location: index.php?controller=Redirection&action=openButterflyWay");
        exit;
    }

    /**
     * Énigme 3 - Labyrinthe
     */
    public function validateButterflyCode()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            $_SESSION['flash_error'] = "Erreur : veuillez choisir une équipe ou vous reconnecter.";
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        $code = $this->normalize($_POST['code'] ?? '');
        if ($code === '') {
            $_SESSION['flash_error'] = "Code vide.";
            header("Location: index.php?controller=Redirection&action=openButterflyWay");
            exit;
        }

        $team = $_SESSION['team'];

        $solutions = [
            'alice' => ['admin'],
            'bob'   => ['root'],
        ];

        if (!isset($solutions[$team])) {
            $_SESSION['flash_error'] = "Équipe inconnue.";
            header("Location: index.php?controller=Redirection&action=openButterflyWay");
            exit;
        }

        // Easter-egg optionnel
        $allowed = array_merge($solutions[$team], ['nollipap']);

        if (!in_array($code, $allowed, true)) {
            $_SESSION['flash_error'] = "Mot invalide — réessaie en suivant la logique de la piste.";
            header("Location: index.php?controller=Redirection&action=openButterflyWay");
            exit;
        }

        // Succès -> update level
        $userId = (int)$_SESSION['utilisateur']['id'];
        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 4);

        $_SESSION['flash_success'] = "AUTH OK — Le papillon reprend sa course. La suite s’ouvre.";
        header("Location: index.php?controller=Redirection&action=openPhishingPuzzle");
        exit;
    }

    /**
     * Énigme 4 - Phishing Par Mail
     */
    // Affichage de message lors du retour depuis une page de phishing
    public function phishingLinkClick()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Message d'avertissement
        $_SESSION['flash_error'] = "Attention ! Vous ne devriez pas cliquer sur des liens suspects dans un courriel 
        non vérifié.";

        // Mémorise l'état pour réouvrir le mail fautif
        $_SESSION['phishing_state'] = [
            'answer' => $_SESSION['phishing_state']['answer'] ?? '',
            'open_mail' => $_GET['from_id'] ?? 1, // On récupère l'ID du mail pour le réouvrir
            'open_pdf' => false
        ];

        header("Location: index.php?controller=Redirection&action=openPhishingPuzzle");
        exit;
    }

    public function openFacebookPhishing()
    {
        ViewHandler::show('phishingpages/facebookPhishingView', ['pageTitle' => 'Facebook Login']);
    }

    public function openImpotsPhishing()
    {
        ViewHandler::show('phishingpages/impotsPhishingView', ['pageTitle' => 'Impôts Gouv']);
    }

    public function openGenealogiePhishing()
    {
        ViewHandler::show('phishingpages/genealogiePhishingView', ['pageTitle' => 'Généalogie Direct']);
    }

    public function openColisPhishing()
    {
        ViewHandler::show('phishingpages/colisPhishingView', ['pageTitle' => 'Suivi de Colis']);
    }

    public function openVideoPhishing()
    {
        ViewHandler::show('phishingpages/videoPhishingView', ['pageTitle' => 'Contenu Multimédia']);
    }

    public function validatePhishing()
    {
        // Démarre la session si elle n'est pas déjà active
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Vérifie que l'utilisateur est bien connecté
        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php");
            exit;
        }

        // Récupère et normalise la réponse du formulaire
        $answerRaw = $_POST['answer'] ?? '';
        $answer = $this->normalize($answerRaw);

        // Réponse de l'énigme, on accepte tous les réponses contenant le mot 'tante'
        if (str_contains($answer, 'tante')) {
            $userId = $_SESSION['utilisateur']['id'];
            $progressModel = new GameProgressModel();

            // L'énigme Phishing est le niveau 4 (normalement) donc on va au niveau 5
            $progressModel->updateLevel($userId, 5);

            // Nettoie l'état mémorisé en cas de succès
            unset($_SESSION['phishing_state']);
            $_SESSION['flash_success'] = "Bravo ! Vous avez compris le lien de parenté.";
            header("Location: index.php?controller=Redirection&action=openPasswordGame");
        } else {
            // En cas d'erreur, mémorise l'état pour restaurer l'interface après rechargement
            $_SESSION['phishing_state'] = [
                'answer' => $answerRaw,
                'open_mail' => 3,
                'open_pdf' => true
            ];
            $_SESSION['flash_error'] = "Ce n'est pas la bonne réponse. Relisez bien le document";
            header("Location: index.php?controller=Redirection&action=openPhishingPuzzle");
        }
        exit;
    }

    /**
     * Énigme 5 - Password Game
     */
    public function validatePasswordGame()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php");
            exit;
        }

        $userId = $_SESSION['utilisateur']['id'];
        $progressModel = new GameProgressModel();

        // On passe au niveau 6
        $progressModel->updateLevel($userId, 6);

        $_SESSION['flash_success'] = "Épreuve du mot de passe réussie !";

        header("Location: index.php?controller=Redirection&action=openSummaryClue");
        exit;
    }

    /**
     * Enigme 6 - Résumé des indices
     */
    public function valideSummary()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            header("Location: index.php");
            exit;
        }

        $team = $_SESSION['team'];
        $ans = $_POST['answer'] ?? '';

        $ans1 = $this->normalize($ans);

        if ($ans1 === '') {
            $_SESSION['flash_error'] = "Votre réponse est vide. Veuillez entrer une réponse.";
            header("Location: index.php?controller=Redirection&action=openSummaryClue");
            exit;
        }

        // mots acceptés selon l'équipe
        $solutions = [
            'alice' => ['cousin', 'cousins'],
            'bob'   => ['cousine', 'cousines']
        ];

        $words = preg_split('/\s+/', $ans1);
        $valid = false;

        foreach ($words as $word) {
            if (in_array($word, $solutions[$team])) {
                $valid = true;
                break;
            }
        }

        if ($valid) {
            $userId = $_SESSION['utilisateur']['id'];
            $progressModel = new GameProgressModel();
            $progressModel->updateLevel($userId, 7);
            $_SESSION['flash_success'] = "Bravo ! Vous avez compris le lien de parenté et avancé dans l’enquête.";
            header("Location: index.php?controller=Redirection&action=openSearchSM");
        } else {
            $_SESSION['flash_error'] = "Non, ce n’est pas la bonne réponse. Relisez bien les indices.";
            header("Location: index.php?controller=Redirection&action=openSummaryClue");
        }

        exit;
    }

    /**
     * Epreuve 8 - Mot de passe commun
     */
    public function valideMotCle()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }
        $input = $this->normalize($_POST['answer1'] ?? '');
        $Word1 = 'papillon';
        $Word2 = 'clé';
        if (str_contains($input, $Word1) && str_contains($input, $Word2)) {
            $userId = $_SESSION['utilisateur']['id'];
            $progressModel = new GameProgressModel();
            $progressModel->updateLevel($userId, 9);

            $_SESSION['flash_success'] = "Bravo ! L'union fait la force";
            header("Location: index.php?controller=Redirection&action=openEndText");
            exit;
        } else {
            $_SESSION['flash_error'] = "Le mot de passe est incomplet ou incorrect. 
            N'oubliez pas qu'il faut combiner les indices des deux équipes.";
            header("Location: index.php?controller=Redirection&action=openMeetingPwd");
            exit;
        }
    }

    /**
     * Epreuve finale - Trouver où est le coffre
     */
    public function validateEnd()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            header("Location: index.php");
            exit;
        }

        $answer1 = strtolower(trim($_POST['answer1'] ?? ''));
        $answer2 = strtolower(trim($_POST['answer2'] ?? ''));
        if ($answer1 === '' || $answer2 === '') {
            $_SESSION['flash_error'] = "Réponse vide.";
            header("Location: index.php?controller=Redirection&action=openEndText");
            exit;
        }

        $solutionsLieu = [
            'alice' => ['d9', '9d'],
            'bob'   => ['d9', '9d']
        ];

        $team = $_SESSION['team'];
        if (!in_array($answer1, $solutionsLieu[$team])) {
            $_SESSION['flash_error'] = "Ce n’est pas le bonne endroit.";
            header("Location: index.php?controller=Redirection&action=openEndText");
            exit;
        }

        $solutionsCode = [
            'alice' => ['1803'],
            'bob'   => ['1803']
        ];

        if (!in_array($answer2, $solutionsCode[$team])) {
            $_SESSION['flash_error'] = "Ce n’est pas le bon code.";
            header("Location: index.php?controller=Redirection&action=openEndText");
            exit;
        }

        // Fin du jeu
        $userId = $_SESSION['utilisateur']['id'];
        $progressModel = new GameProgressModel();
        $progressModel->finishGame($userId);
        $_SESSION['flash_success'] = "Félicitations ! Tu as trouvé où est le coffre.";

        header("Location: index.php?controller=Redirection&action=openVictory");
        exit;
    }

    public function sendDmMessage()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        $message = trim($_POST['message'] ?? '');

        if ($message === '') {
            header("Location: index.php?controller=Redirection&action=openSocialMediaPuzzle");
            exit;
        }

        $team = $_SESSION['team'];
        $botReplies = [
            'alice' => "Haha oui, quelle coïncidence ! 😄 On se retrouve à la 
            Bibliothèque municipale de Lyon, 30 boulevard Vivier-Merle. Je t'y attends demain à 14h 📚",
            'bob'   => "Trop bien ! 😊 On se voit au Jardin des Curiosités, 
            2 montée des Soldats, Lyon. Je serai là à 14h30 🌿",
        ];

        // Réponse si le mot-clé est absent
        $botFallback = "T'es qui ???";

        // Initialiser l'historique
        if (!isset($_SESSION['ig_messages'])) {
            $_SESSION['ig_messages'] = [];
        }

        // Sauvegarder le message de l'équipe
        $_SESSION['ig_messages'][] = ['from' => 'me', 'text' => $message];

        // Vérifier si le message contient "cousin" ou "cousine"
        $normalized = $this->normalize($message);
        $hasKeyword = str_contains($normalized, 'cousin');

        if ($hasKeyword && isset($botReplies[$team])) {
            $reply = $botReplies[$team];
            $_SESSION['ig_messages'][]     = ['from' => 'bot', 'text' => $reply];
            $_SESSION['ig_bot_replied']    = true;
            $_SESSION['ig_bot_reply_text'] = $reply;
        } else {
            $reply = $botFallback;
            $_SESSION['ig_messages'][] = ['from' => 'bot', 'text' => $reply];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'reply'      => $reply,
            'botReplied' => $_SESSION['ig_bot_replied'] ?? false,
        ]);
        exit;
    }
    public function sendDecoyDmMessage()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        $message = trim($_POST['message'] ?? '');
        $handle  = trim($_POST['decoy_handle'] ?? 'inconnu');

        if ($message === '') {
            header("Location: index.php?controller=Redirection&action=openSocialMediaPuzzle");
            exit;
        }

        // Initialiser l'historique leurre séparé par handle
        $key = 'ig_decoy_messages_' . preg_replace('/[^a-z0-9_]/', '_', strtolower($handle));
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }

        // Sauvegarder le message du joueur
        $_SESSION[$key][] = ['from' => 'me', 'text' => $message];

        // Réponses phishing des faux comptes
        $decoyReplies = [
            // Leurres équipe Alice (faux Bob)
            'bob_martin'    => "Salut ! Clique sur ce lien pour qu'on se retrouve 👉 bit.ly/r3nd3zv0us-secret 😊",
            'bob.leblanc'   => "Hey ! J'ai quelque chose à te montrer 👉 bit.ly/secret-meet-up 🤫",
            'bobby.photos'  => "Coucou ! Regarde ça, c'est pour toi 👉 bit.ly/surprise-link 😄",
            'bob_aventures' => "Yo ! Viens voir ici 👉 bit.ly/rdv-prive 🔗",
            // Leurres équipe Bob (faux Alice)
            'alice_martin'  => "Salut ! Clique sur ce lien pour qu'on se retrouve 👉 bit.ly/r3nd3zv0us-secret 😊",
            'alice.photo'   => "Hey ! J'ai quelque chose à te montrer 👉 bit.ly/secret-meet-up 🤫",
            'alicedupont__' => "Coucou ! Regarde ça, c'est pour toi 👉 bit.ly/surprise-link 😄",
            'alice_cuisine' => "Yo ! Viens voir ici 👉 bit.ly/rdv-prive 🔗",
        ];

        $reply = $decoyReplies[$handle]
            ?? "Salut ! Clique sur ce lien pour qu'on se retrouve 👉 bit.ly/r3nd3zv0us-secret 😊";

        $_SESSION[$key][] = ['from' => 'bot', 'text' => $reply];

        // Retourner les messages en JSON pour le JS
        header('Content-Type: application/json');
        echo json_encode([
            'reply'    => $reply,
            'messages' => $_SESSION[$key],
        ]);
        exit;
    }

    public function validateSocialMedia()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        if (empty($_SESSION['ig_bot_replied'])) {
            $_SESSION['flash_error'] = "Tu dois d'abord obtenir une réponse dans les messages.";
            header("Location: index.php?controller=Redirection&action=openSearchSM");
            exit;
        }

        // Nettoyage des données de l'épreuve
        unset($_SESSION['ig_messages'], $_SESSION['ig_bot_replied'], $_SESSION['ig_bot_reply_text']);

        $userId        = (int) $_SESSION['utilisateur']['id'];
        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 8);

        $_SESSION['flash_success'] = "Bravo ! Tu as obtenu la localisation et validé l'épreuve.";
        header("Location: index.php?controller=Redirection&action=openMeetingPwd");
        exit;
    }
}
