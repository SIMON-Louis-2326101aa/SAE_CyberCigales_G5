<?php

namespace SAE_CyberCigales_G5\Modules\controller;

class TeamController
{
    public function setTeam()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_GET['team'])) {
            header("Location: index.php?controller=Redirection&action=openChoice");
            exit;
        }

        $team = $_GET['team'];

        $_SESSION['team'] = $team;

        if ($team === "alice") {
            header("Location: index.php?controller=Redirection&action=openLetterIntro&team=alice");
        } else {
            header("Location: index.php?controller=Redirection&action=openLetterIntro&team=bob");
        }

        exit;
    }
}
