<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\Modules\model\GameProgressModel;
use SAE_CyberCigales_G5\includes\ViewHandler;

class PuzzleController
{
    private function logPuzzle(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    /**
     * Normalise un texte pour comparaison fiable
     */
    private function normalize(string $text): string
    {
        // minuscule
        $text = mb_strtolower($text, 'UTF-8');

        // uniformiser apostrophes
        $text = str_replace(["’", "`", "´"], "'", $text);

        // supprimer accents
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        // normaliser espaces
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
            $this->logPuzzle('Énigme 1 refusée: session utilisateur ou équipe absente', 'warn');
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];
        $team = $_SESSION['team'];

        // RÉCUPÉRATION RÉPONSES
        $answerLetter = $_POST['answer1'] ?? '';
        $answerMorse  = $_POST['answer2'] ?? '';
        $_SESSION['old_answer1'] = $answerLetter;
        $_SESSION['old_answer2'] = $answerMorse;

        $this->logPuzzle('Énigme 1 soumise', 'file', [
            'user_id' => $userId,
            'team' => $team,
            'has_letter' => trim($answerLetter) !== '',
            'has_morse' => trim($answerMorse) !== '',
        ]);

        if (trim($answerLetter) === '' || trim($answerMorse) === '') {
            $_SESSION['flash_error'] = "Les deux réponses doivent être remplies.";
            $this->logPuzzle('Énigme 1 échouée: réponse vide', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // SOLUTIONS
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
            $this->logPuzzle('Énigme 1 échouée: équipe inconnue', 'error', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // VALIDATION CÉSAR
        $answerLetterNorm = $this->normalize($answerLetter);
        $expectedLetter   = $this->normalize($letterSolutions[$team]);

        if ($answerLetterNorm !== $expectedLetter) {
            $_SESSION['flash_error'] = "La lettre n’a pas été correctement déchiffrée.";
            $this->logPuzzle('Énigme 1 échouée: lettre incorrecte', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // VALIDATION MORSE
        $answerMorseNorm = $this->normalize($answerMorse);
        $expectedMorse   = $this->normalize($morseSolutions[$team]);

        if ($answerMorseNorm !== $expectedMorse) {
            $_SESSION['flash_error'] = "Le message codé est incorrect.";
            $this->logPuzzle('Énigme 1 échouée: morse incorrect', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        unset($_SESSION['old_answer1'], $_SESSION['old_answer2']);

        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 2);

        $_SESSION['flash_success'] = "Bravo ! Les deux messages ont été correctement déchiffrés.";

        $this->logPuzzle('Énigme 1 réussie', 'ok', [
            'user_id' => $userId,
            'team' => $team,
            'new_level' => 2,
        ]);

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
            $this->logPuzzle('Énigme 2 refusée: session utilisateur ou équipe absente', 'warn');
            header("Location: index.php");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];
        $team = $_SESSION['team'];

        $answer = strtolower(trim($_POST['answer'] ?? ''));

        $this->logPuzzle('Énigme 2 soumise', 'file', [
            'user_id' => $userId,
            'team' => $team,
            'has_answer' => $answer !== '',
        ]);

        if ($answer === '') {
            $_SESSION['flash_error'] = "Réponse vide.";
            $this->logPuzzle('Énigme 2 échouée: réponse vide', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openPicturePuzzle");
            exit;
        }

        $solutions = [
            'alice' => ['mémoire', 'papillon'],
            'bob'   => ['passé', 'clé']
        ];

        $isValid = false;

        foreach ($solutions[$team] as $word) {
            if (str_contains($answer, $word)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            $_SESSION['flash_error'] = "Ce n’est pas la bonne interprétation.";
            $this->logPuzzle('Énigme 2 échouée: interprétation incorrecte', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openPicturePuzzle");
            exit;
        }

        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 3);

        $_SESSION['flash_success'] = "Bien joué, tu avances dans l’enquête.";

        $this->logPuzzle('Énigme 2 réussie', 'ok', [
            'user_id' => $userId,
            'team' => $team,
            'new_level' => 3,
        ]);

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
            $this->logPuzzle('Énigme 3 refusée: session utilisateur ou équipe absente', 'warn');
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];
        $team = $_SESSION['team'];

        $code = $this->normalize($_POST['code'] ?? '');

        $this->logPuzzle('Énigme 3 soumise', 'file', [
            'user_id' => $userId,
            'team' => $team,
            'has_code' => $code !== '',
        ]);

        if ($code === '') {
            $_SESSION['flash_error'] = "Code vide.";
            $this->logPuzzle('Énigme 3 échouée: code vide', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openButterflyWay");
            exit;
        }

        $solutions = [
            'alice' => ['admin'],
            'bob'   => ['root'],
        ];

        if (!isset($solutions[$team])) {
            $_SESSION['flash_error'] = "Équipe inconnue.";
            $this->logPuzzle('Énigme 3 échouée: équipe inconnue', 'error', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openButterflyWay");
            exit;
        }

        $allowed = array_merge($solutions[$team], ['nollipap']);

        if (!in_array($code, $allowed, true)) {
            $_SESSION['flash_error'] = "Mot invalide — réessaie en suivant la logique de la piste.";
            $this->logPuzzle('Énigme 3 échouée: code incorrect', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openButterflyWay");
            exit;
        }

        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 4);

        $_SESSION['flash_success'] = "AUTH OK — Le papillon reprend sa course. La suite s’ouvre.";

        $this->logPuzzle('Énigme 3 réussie', 'ok', [
            'user_id' => $userId,
            'team' => $team,
            'new_level' => 4,
            'used_easter_egg' => $code === 'nollipap',
        ]);

        header("Location: index.php?controller=Redirection&action=openPhishingPuzzle");
        exit;
    }

    /**
     * Énigme 4 - Phishing Par Mail
     */
    public function phishingLinkClick()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['flash_error'] = "Attention ! Vous ne devriez pas cliquer sur des liens suspects dans un courriel 
        non vérifié.";

        $_SESSION['phishing_state'] = [
            'answer' => $_SESSION['phishing_state']['answer'] ?? '',
            'open_mail' => $_GET['from_id'] ?? 1,
            'open_pdf' => false
        ];

        $this->logPuzzle('Énigme 4: clic sur lien phishing détecté', 'warn', [
            'user_id' => $_SESSION['user_id'] ?? null,
            'from_id' => $_GET['from_id'] ?? 1,
        ]);

        header("Location: index.php?controller=Redirection&action=openPhishingPuzzle");
        exit;
    }

    public function openFacebookPhishing()
    {
        $this->logPuzzle('Ouverture faux Facebook', 'file', [
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);
        ViewHandler::show('phishingpages/facebookPhishingView', ['pageTitle' => 'Facebook Login']);
    }

    public function openImpotsPhishing()
    {
        $this->logPuzzle('Ouverture faux site impôts', 'file', [
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);
        ViewHandler::show('phishingpages/impotsPhishingView', ['pageTitle' => 'Impôts Gouv']);
    }

    public function openGenealogiePhishing()
    {
        $this->logPuzzle('Ouverture faux site généalogie', 'file', [
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);
        ViewHandler::show('phishingpages/genealogiePhishingView', ['pageTitle' => 'Généalogie Direct']);
    }

    public function openColisPhishing()
    {
        $this->logPuzzle('Ouverture faux site colis', 'file', [
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);
        ViewHandler::show('phishingpages/colisPhishingView', ['pageTitle' => 'Suivi de Colis']);
    }

    public function openVideoPhishing()
    {
        $this->logPuzzle('Ouverture faux contenu vidéo', 'file', [
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);
        ViewHandler::show('phishingpages/videoPhishingView', ['pageTitle' => 'Contenu Multimédia']);
    }

    public function validatePhishing()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'])) {
            $this->logPuzzle('Énigme 4 refusée: utilisateur absent', 'warn');
            header("Location: index.php");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];

        $answerRaw = $_POST['answer'] ?? '';
        $answer = $this->normalize($answerRaw);

        $this->logPuzzle('Énigme 4 soumise', 'file', [
            'user_id' => $userId,
            'has_answer' => $answer !== '',
        ]);

        if (str_contains($answer, 'tante')) {
            $progressModel = new GameProgressModel();
            $progressModel->updateLevel($userId, 5);

            unset($_SESSION['phishing_state']);
            $_SESSION['flash_success'] = "Bravo ! Vous avez compris le lien de parenté.";

            $this->logPuzzle('Énigme 4 réussie', 'ok', [
                'user_id' => $userId,
                'new_level' => 5,
            ]);

            header("Location: index.php?controller=Redirection&action=openPasswordGame");
        } else {
            $_SESSION['phishing_state'] = [
                'answer' => $answerRaw,
                'open_mail' => 3,
                'open_pdf' => true
            ];
            $_SESSION['flash_error'] = "Ce n'est pas la bonne réponse. Relisez bien le document";

            $this->logPuzzle('Énigme 4 échouée: mauvaise réponse', 'warn', [
                'user_id' => $userId,
            ]);

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
            $this->logPuzzle('Énigme 5 refusée: utilisateur absent', 'warn');
            header("Location: index.php");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];
        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 6);

        $_SESSION['flash_success'] = "Épreuve du mot de passe réussie !";

        $this->logPuzzle('Énigme 5 réussie', 'ok', [
            'user_id' => $userId,
            'new_level' => 6,
        ]);

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
            $this->logPuzzle('Énigme 6 refusée: session utilisateur ou équipe absente', 'warn');
            header("Location: index.php");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];
        $team = $_SESSION['team'];
        $ans = $_POST['answer'] ?? '';
        $ans1 = $this->normalize($ans);

        $this->logPuzzle('Énigme 6 soumise', 'file', [
            'user_id' => $userId,
            'team' => $team,
            'has_answer' => $ans1 !== '',
        ]);

        if ($ans1 === '') {
            $_SESSION['flash_error'] = "Votre réponse est vide. Veuillez entrer une réponse.";
            $this->logPuzzle('Énigme 6 échouée: réponse vide', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openSummaryClue");
            exit;
        }

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
            $progressModel = new GameProgressModel();
            $progressModel->updateLevel($userId, 7);
            $_SESSION['flash_success'] = "Bravo ! Vous avez compris le lien de parenté et avancé dans l’enquête.";

            $this->logPuzzle('Énigme 6 réussie', 'ok', [
                'user_id' => $userId,
                'team' => $team,
                'new_level' => 7,
            ]);

            header("Location: index.php?controller=Redirection&action=openSearchSM");
        } else {
            $_SESSION['flash_error'] = "Non, ce n’est pas la bonne réponse. Relisez bien les indices.";

            $this->logPuzzle('Énigme 6 échouée: mauvaise réponse', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);

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
            $this->logPuzzle('Épreuve mot-clé refusée: utilisateur absent', 'warn');
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];
        $input = $this->normalize($_POST['answer1'] ?? '');
        $word1 = 'papillon';
        $word2 = 'clé';

        $this->logPuzzle('Mot-clé commun soumis', 'file', [
            'user_id' => $userId,
            'has_input' => $input !== '',
        ]);

        if (str_contains($input, $word1) && str_contains($input, $word2)) {
            $progressModel = new GameProgressModel();
            $progressModel->updateLevel($userId, 9);

            $_SESSION['flash_success'] = "Bravo ! L'union fait la force";

            $this->logPuzzle('Mot-clé commun réussi', 'ok', [
                'user_id' => $userId,
                'new_level' => 9,
            ]);

            header("Location: index.php?controller=Redirection&action=openEndText");
            exit;
        }

        $_SESSION['flash_error'] = "Le mot de passe est incomplet ou incorrect. 
            N'oubliez pas qu'il faut combiner les indices des deux équipes.";

        $this->logPuzzle('Mot-clé commun échoué', 'warn', [
            'user_id' => $userId,
        ]);

        header("Location: index.php?controller=Redirection&action=openMeetingPwd");
        exit;
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
            $this->logPuzzle('Épreuve finale refusée: session utilisateur ou équipe absente', 'warn');
            header("Location: index.php");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];
        $team = $_SESSION['team'];

        $answer1 = strtolower(trim($_POST['answer1'] ?? ''));
        $answer2 = strtolower(trim($_POST['answer2'] ?? ''));

        $this->logPuzzle('Épreuve finale soumise', 'file', [
            'user_id' => $userId,
            'team' => $team,
            'has_location' => $answer1 !== '',
            'has_code' => $answer2 !== '',
        ]);

        if ($answer1 === '' || $answer2 === '') {
            $_SESSION['flash_error'] = "Réponse vide.";
            $this->logPuzzle('Épreuve finale échouée: réponse vide', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openEndText");
            exit;
        }

        $solutionsLieu = [
            'alice' => ['d9', '9d'],
            'bob'   => ['d9', '9d']
        ];

        if (!in_array($answer1, $solutionsLieu[$team])) {
            $_SESSION['flash_error'] = "Ce n’est pas le bonne endroit.";
            $this->logPuzzle('Épreuve finale échouée: mauvaise localisation', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openEndText");
            exit;
        }

        $solutionsCode = [
            'alice' => ['1803'],
            'bob'   => ['1803']
        ];

        if (!in_array($answer2, $solutionsCode[$team])) {
            $_SESSION['flash_error'] = "Ce n’est pas le bon code.";
            $this->logPuzzle('Épreuve finale échouée: mauvais code', 'warn', [
                'user_id' => $userId,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openEndText");
            exit;
        }

        $progressModel = new GameProgressModel();
        $progressModel->finishGame($userId);
        $_SESSION['flash_success'] = "Félicitations ! Tu as trouvé où est le coffre.";

        $this->logPuzzle('Épreuve finale réussie', 'ok', [
            'user_id' => $userId,
            'team' => $team,
            'game_finished' => true,
        ]);

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
            'alice' => "Non vraiment, on doit se voir !",
            'bob'   => "tu me mens pas quand même, viens on se voit !",
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
            'bob_martin'    => "Salut je suis vraiment désolé je te connais pas",
            'bobby.photos'  => "Salut désolé je ne connais pas",
            'bob_aventures' => "Salut t'es pas sélectionné",
            // Leurres équipe Bob (faux Alice)
            'alice.photo'   => "Désolé je ne te connais pas",
            'alicedupont__' => "C'est vrai, mais incroyable alors c'est toi sur cette photo ",
            'alice_cuisine' => "Je n'aimes pas parler aux inconnus dsl",
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
