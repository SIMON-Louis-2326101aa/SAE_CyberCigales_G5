<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\Modules\model\GameProgressModel;

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
                Il est des blessures que l’on tait trop longtemps, croyant qu’elles disparaîtront
                d’elles-mêmes. Mais le silence ne soigne pas, il endort seulement la douleur.
                Nous avons vu, dans le regard de ta fille Alice, cette même lueur que tu avais enfant :
                celle de la curiosité et du courage mêlés.
                Ne la laisse pas s’éteindre, même si le monde tente de la couvrir d’ombre.
                Tout ce que nous avons construit, tout ce que nous avons caché, nous l’avons fait
                pour que quelqu’un comme elle puisse un jour comprendre.
                Avec toute la tendresse que le vent n’a pas emportée.
                Tes grands-parents qui t'aiment.
            ",

            'bob' => "
                Ma chère Clara,
                Le temps nous a glissé entre les doigts comme du sable, mais avant qu’il ne disparaisse
                complètement, nous voulions te confier ces mots.
                Il n’y a pas de faute trop ancienne pour être pardonnée, ni de distance trop grande
                pour être franchie.
                Parfois, la vie nous sépare non pour nous punir, mais pour nous apprendre à revenir.
                Ton fils Bob possède déjà cette flamme que nous avons reconnue : la soif de comprendre,
                d’aller au-delà des évidences.
                Aide-le à écouter ce qu’on ne dit pas, à lire ce qu’on ne montre plus.
                Ce que nous avons laissé derrière nous n’est pas un trésor d’or ou de pierre, mais un
                message, une part de notre histoire, cachée dans les plis du temps.
                Avec l’espoir que les chemins perdus se croisent à nouveau.
                Tes grands-parents qui t'aiment.
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

        //VALIDATION CÉSAR
        $answerLetterNorm = $this->normalize($answerLetter);
        $expectedLetter   = $this->normalize($letterSolutions[$team]);

        if ($answerLetterNorm !== $expectedLetter) {
            $_SESSION['flash_error'] =
                "La lettre n’a pas été correctement déchiffrée.";
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // VALIDATION MORSE
        $answerMorseNorm = $this->normalize($answerMorse);
        $expectedMorse   = $this->normalize($morseSolutions[$team]);

        if ($answerMorseNorm !== $expectedMorse) {
            $_SESSION['flash_error'] =
                "Le message codé est incorrect.";
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        //SUCCÈS → SUITE
        $userId = $_SESSION['utilisateur']['id'];

        $progressModel = new GameProgressModel();
        $progressModel->updateLevel($userId, 2);

        $_SESSION['flash_success'] =
            "Bravo ! Les deux messages ont été correctement déchiffrés.";

        header("Location: index.php?controller=Redirection&action=openPicturePuzzle");
        exit;
    }

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
    // Dans PuzzleController.php, modifiez la fonction valideIndice :

    public function valideIndice()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'], $_SESSION['team'])) {
            header("Location: index.php");
            exit;
        }

        $team = $_SESSION['team'];
        $epreuve = $_POST['epreuve'] ?? '';
        $ans1Raw = $_POST['answer1'] ?? '';
        $ans2Raw = $_POST['answer2'] ?? '';

        $ans1 = $this->normalize($ans1Raw);
        $ans2 = $this->normalize($ans2Raw);
        if (!isset($_SESSION['saved_indices'])) {
            $_SESSION['saved_indices'] = [];
        }

        if ($ans1 === '' || $ans2 === '') {
            $_SESSION['flash_error'] = "Tous les indices doivent être remplis.";
            header("Location: index.php?controller=Redirection&action=openSummaryClue");
            exit;
        }

        $solutions = [
            'alice' => [
                '1' => ['indice1' => 'solution1', 'indice2' => 'solution2'],
                '2' => ['indice1' => 'solution3', 'indice2' => 'solution4'],
                '3' => ['indice1' => 'solution5', 'indice2' => 'solution6'],
            ],
            'bob' => [
                '1' => ['indice1' => 'valeur1', 'indice2' => 'valeur2'],
                '2' => ['indice1' => 'valeur3', 'indice2' => 'valeur4'],
                '3' => ['indice1' => 'valeur5', 'indice2' => 'valeur6'],
            ]
        ];

        $expected = $solutions[$team][$epreuve] ?? null;

        if ($expected) {
            $isCorrect1 = ($ans1 === $this->normalize($expected['indice1']));
            $isCorrect2 = ($ans2 === $this->normalize($expected['indice2']));

            // On ne sauvegarde en session QUE si c'est correct
            if ($isCorrect1) {
                $_SESSION['saved_indices'][$team][$epreuve]['ans1'] = $ans1Raw;
            }
            if ($isCorrect2) {
                $_SESSION['saved_indices'][$team][$epreuve]['ans2'] = $ans2Raw;
            }

            if ($isCorrect1 && $isCorrect2) {
                $_SESSION['flash_success'] = "Indices de l'épreuve $epreuve validés !";
            } else {
                $_SESSION['flash_error'] = "Certains indices pour l'épreuve $epreuve sont incorrects.";
            }
        }

        header("Location: index.php?controller=Redirection&action=openSummaryClue");
        exit;
    }

    public function validatePhishing()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php");
            exit;
        }

        $answer = $this->normalize($_POST['answer'] ?? '');
        
        // On accepte "tante" ou "ma tante"
        if (str_contains($answer, 'tante')) {
            $userId = $_SESSION['utilisateur']['id'];
            $progressModel = new GameProgressModel();
            
            // L'énigme Phishing est le niveau 7 (normalement)
            $progressModel->updateLevel($userId, 7);

            $_SESSION['flash_success'] = "Bravo ! Vous avez compris le lien de parenté.";
            header("Location: index.php?controller=Redirection&action=openPasswordGame");
        } else {
            $_SESSION['flash_error'] = "Ce n'est pas la bonne réponse. Relisez bien l'acte de naissance.";
            header("Location: index.php?controller=Redirection&action=openPhishingPuzzle");
        }
        exit;
    }
}
