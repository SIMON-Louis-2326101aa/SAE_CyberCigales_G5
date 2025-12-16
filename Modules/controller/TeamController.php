<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\Modules\model\GameProgressModel;

class TeamController
{
//    private GameProgressModel $gameProgressModel;
//    public function __construct()
//    {
//        $this->gameProgressModel = new GameProgressModel();
//    }
    public function setTeam()
    {
        session_start();

        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php?controller=Redirection&action=openFormConnection");
            exit;
        }

        $team = $_GET['team'] ?? null;
        if (!in_array($team, ['alice', 'bob'])) {
            header("Location: index.php?controller=Redirection&action=openChoice");
            exit;
        }

        $userId = $_SESSION['utilisateur']['id'];

        $progressModel = new GameProgressModel();
        $existing = $progressModel->getByUserId($userId);

        // ÉQUIPE DÉJÀ CHOISIE
        if ($existing) {
            $_SESSION['flash_error'] = "Équipe déjà sélectionnée.";
            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // PREMIER CHOIX
        $progressModel->create($userId, $team);
        $_SESSION['team'] = $team;
        $_SESSION['game_start_time'] = time();

        header("Location: index.php?controller=Redirection&action=openLetterIntro");
        exit;
    }

    public function play()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // 1. Pas connecté → login
        if (!isset($_SESSION['utilisateur'])) {
            header("Location: index.php?controller=Redirection&action=openFormConnection");
            exit;
        }

        $userId = $_SESSION['utilisateur']['id'];

        $progressModel = new GameProgressModel();
        $progress = $progressModel->getByUserId($userId);

        // 2. Pas encore commencé → choix équipe
        if (!$progress) {
            header("Location: index.php?controller=Redirection&action=openChoice");
            exit;
        }

        // 3. Déjà une progression → reprendre
        $_SESSION['team'] = $progress['team'];

        switch ((int)$progress['level']) {
            case 1:
                header("Location: index.php?controller=Redirection&action=openLetterIntro");
                break;

//            case 2:
//                header("Location: index.php?controller=Redirection&action=openPuzzleCesar");
//                break;
//
//            case 3:
//                header("Location: index.php?controller=Redirection&action=openPuzzleSuivant");
//                break;

            default:
                header("Location: index.php?controller=Redirection&action=openHomepage");
        }

        exit;
    }
}
