<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\Modules\model\GameProgressModel;
use SAE_CyberCigales_G5\Modules\model\UserModel;

class TeamController
{
    private function logTeam(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    public function setTeam()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['utilisateur'])) {
            $this->logTeam('Choix équipe refusé: utilisateur non connecté', 'warn');
            header("Location: index.php?controller=Redirection&action=openFormConnection");
            exit;
        }

        $team = $_GET['team'] ?? null;

        if (!in_array($team, ['alice', 'bob'], true)) {
            $this->logTeam('Choix équipe refusé: équipe invalide', 'warn', [
                'user_id' => $_SESSION['utilisateur']['id'] ?? null,
                'team' => $team,
            ]);
            header("Location: index.php?controller=Redirection&action=openChoice");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];

        $progressModel = new GameProgressModel();
        $userModel = new UserModel();
        $existing = $progressModel->getByUserId($userId);

        $isAdmin = isset($_SESSION['email']) && $_SESSION['email'] === $_ENV['ADMIN_EMAIL'];

        $this->logTeam('Choix équipe demandé', 'file', [
            'user_id' => $userId,
            'team' => $team,
            'is_admin' => $isAdmin,
            'has_existing_progress' => $existing !== null,
        ]);

        // ÉQUIPE DÉJÀ CHOISIE : bloqué pour user, autorisé pour admin
        if ($existing) {
            if (!$isAdmin) {
                $_SESSION['flash_error'] = "Équipe déjà sélectionnée.";

                $this->logTeam('Choix équipe refusé: progression déjà existante pour utilisateur non admin', 'warn', [
                    'user_id' => $userId,
                    'team' => $team,
                    'existing_team' => $existing['team'] ?? null,
                    'level' => $existing['level'] ?? null,
                ]);

                header("Location: index.php?controller=Redirection&action=openLetterIntro");
                exit;
            }

            // admin : peut re-choisir la team + reset uniquement le timer (pas le level)
            $progressModel->updateTeam($userId, $team);
            $progressModel->resetTimer($userId);

            $_SESSION['team'] = $team;
            $_SESSION['game_start_time'] = time();

            $progressModel->startOrResumeGame($userId);

            $this->logTeam('Choix équipe admin appliqué avec reset timer', 'ok', [
                'user_id' => $userId,
                'team' => $team,
            ]);

            header("Location: index.php?controller=Redirection&action=openLetterIntro");
            exit;
        }

        // PREMIER CHOIX
        $progressModel->create($userId, $team);
        $_SESSION['team'] = $team;
        $_SESSION['game_start_time'] = time();
        $progressModel->startOrResumeGame($userId);

        $newNbTry = $userModel->incrementNbTry($userId);
        $_SESSION['utilisateur']['nbTry'] = $newNbTry;

        $this->logTeam('Premier choix équipe enregistré', 'ok', [
            'user_id' => $userId,
            'team' => $team,
            'new_nb_try' => $newNbTry,
        ]);

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
            $this->logTeam('Reprise partie refusée: utilisateur non connecté', 'warn');
            header("Location: index.php?controller=Redirection&action=openFormConnection");
            exit;
        }

        $userId = (int)$_SESSION['utilisateur']['id'];

        $progressModel = new GameProgressModel();
        $progress = $progressModel->getByUserId($userId);

        // 2. Pas encore commencé → choix équipe
        if (!$progress) {
            $this->logTeam('Reprise partie impossible: aucune progression trouvée', 'info', [
                'user_id' => $userId,
            ]);
            header("Location: index.php?controller=Redirection&action=openChoice");
            exit;
        }

        // 3. Déjà une progression → reprendre
        $_SESSION['team'] = $progress['team'];

        $level = (int)$progress['level'];

        $this->logTeam('Reprise partie demandée', 'file', [
            'user_id' => $userId,
            'team' => $progress['team'] ?? null,
            'level' => $level,
        ]);

        switch ($level) {
            case 1:
                $this->logTeam('Reprise vers niveau 1', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openLetterIntro");
                break;

            case 2:
                $this->logTeam('Reprise vers niveau 2', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openPicturePuzzle");
                break;

            case 3:
                $this->logTeam('Reprise vers niveau 3', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openButterflyWay");
                break;

            case 4:
                $this->logTeam('Reprise vers niveau 4', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openPhishingPuzzle");
                break;

            case 5:
                $this->logTeam('Reprise vers niveau 5', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openPasswordGame");
                break;

            case 6:
                $this->logTeam('Reprise vers niveau 6', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openSummaryClue");
                break;

            case 7:
                $this->logTeam('Reprise vers niveau 7', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openSearchSM");
                break;

            case 8:
                $this->logTeam('Reprise vers niveau 8', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openMeetingPwd");
                break;

            case 9:
                $this->logTeam('Reprise vers niveau 9', 'info', [
                    'user_id' => $userId,
                ]);
                header("Location: index.php?controller=Redirection&action=openEndText");
                break;

            default:
                $this->logTeam('Reprise avec niveau inconnu, retour accueil', 'warn', [
                    'user_id' => $userId,
                    'level' => $level,
                ]);
                header("Location: index.php?controller=Redirection&action=openHomepage");
        }

        exit;
    }
}
