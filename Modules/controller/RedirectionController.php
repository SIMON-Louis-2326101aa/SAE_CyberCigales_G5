<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\ViewHandler;

class RedirectionController
{
    private function logRedirection(string $view): void
    {
        if (function_exists('log_console')) {
            log_console("Redirection vers $view", 'info');
        }
    }
    // petit contrôle optionnel : on bloque si l’utilisateur n’est pas connecté
    private function requireAuth(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['user_id'])) {
            if (function_exists('log_console')) {
                log_console('Accès refusé: non authentifié', 'warn', [
                    'uri' => $_SERVER['REQUEST_URI'] ?? null
                ]);
            }
            $_SESSION['flash_error'] = "Vous devez être connecté pour accéder à cette page.";
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            header('Location: ' . $base . '/index.php?controller=Redirection&action=openFormConnection');
            exit;
        }
    }

    public function openHomepage()
    {
        $this->logRedirection('homepageView');
        ViewHandler::show('homepageView', ['pageTitle' => 'Accueil']);
    }

    public function openLegal()
    {
        $this->logRedirection('legalMentionView');
        ViewHandler::show('legalMentionView', ['pageTitle' => 'Mentions Légales']);
    }

    public function openAccount()
    {
        $this->logRedirection('accountView');
        $this->requireAuth();
        ViewHandler::show('accountView', ['pageTitle' => 'Compte']);
    }

    public function openFormConnection()
    {
        $this->logRedirection('formConnectionView');
        ViewHandler::show('formConnectionView', ['pageTitle' => 'Connexion']);
    }

    public function openFormRegister()
    {
        $this->logRedirection('formRegisterView');
        ViewHandler::show('formRegisterView', ['pageTitle' => 'S\'inscrire']);
    }

    public function openForgotPwd()
    {
        $this->logRedirection('forgotPwdView');
        ViewHandler::show('forgotPwdView', ['pageTitle' => 'Mot de passe oublié']);
    }

    public function openChangePwd()
    {
        $this->logRedirection('changePwdView');
        $this->requireAuth();
        ViewHandler::show('changePwdView', ['pageTitle' => 'Changement de mot de passe', 'token' => $_GET['token']
            ?? '']);
    }

    public function openEmailVerification()
    {
        $this->logRedirection('emailVerificationView');
        ViewHandler::show('emailVerificationView', ['pageTitle' => 'Vérification de l\'e-mail','email' => $_GET['email']
            ?? '']);
    }

    /**
     * Affiche la page "À propos"
     */
    public function openAbout()
    {
        $this->logRedirection('openAbout');
        ViewHandler::show('aboutView', ['pageTitle' => 'À Propos']);
    }

    /* Affiche le plan du Site */
    public function openSiteMap()
    {
        $this->logRedirection('openSiteMap');
        ViewHandler::show('siteMapView', ['pageTitle' => 'Plan du site']);
    }

    /**
     * Affiche la page d'introduction de la lettre
     */
    public function openLetterIntro()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->logRedirection('letterIntroView');
        $this->requireAuth();
        ViewHandler::show('letterIntroView', ['pageTitle' => 'Introduction']);
    }

    /**
     * Affiche la page de l'enigme du papillon
     */
    public function openButterflyWay()
    {
        $this->logRedirection('ButterflyWayView');
        $this->requireAuth();
        ViewHandler::show('butterflyWayView', ['pageTitle' => 'Un Papillon ?']);
    }

    public function openContact()
    {
        $this->logRedirection('contactView');
        ViewHandler::show('contactView', ['pageTitle' => 'Contact']);
    }

    /**
     * Affiche la page de choix du personnage
     */
    public function openChoice()
    {
        $this->logRedirection('choiceView');
        $this->requireAuth();
        ViewHandler::show('choiceView', ['pageTitle' => 'Choix du personnage']);
    }

    /**
     * Affiche la page du puzzle de l'image
     */
    public function openPicturePuzzle()
    {
        $this->logRedirection('openpicturePuzzleView');
        $this->requireAuth();
        ViewHandler::show('picturePuzzleView', ['pageTitle' => 'Enigme de l\'image']);
    }

    /**
     * Affiche la page de l'enigme du mail
     */
    public function openPhishingPuzzle()
    {
        $this->logRedirection('openPhishingPuzzleView');
        $this->requireAuth();
        ViewHandler::show('phishingPuzzleView', ['pageTitle' => 'Enigme du mail']);
    }

    /**
     * Affiche la page du jeu du mot de passe
     */
    public function openPasswordGame()
    {
        $this->logRedirection('openPasswordGameView');
        $this->requireAuth();
        ViewHandler::show('passwordGameView', ['pageTitle' => 'Enigme du jeu du mot de passe']);
    }

    /**
     * Affiche la page de resumé d'indices
     */
    public function openSummaryClue()
    {
        $this->logRedirection('openSummaryClueView');
        $this->requireAuth();
        ViewHandler::show('summaryClueView', ['pageTitle' => 'Enigme du resumé d\'indices']);
    }

    /**
     * Affiche la page de recherche du cousin sur les réseaux
     */
    public function openSearchSM()
    {
        $this->logRedirection('openSearchSMView');
        $this->requireAuth();
        ViewHandler::show('searchSMView', ['pageTitle' => 'Enigme de recherche']);
    }

    /**
     * Affiche la page de rencontre des deux equipes et mot de passe
     */
    public function openMeetingPwd()
    {
        $this->logRedirection('openMeetingPwdView');
        $this->requireAuth();
        ViewHandler::show('meetingPasswordView', ['pageTitle' => 'rassemblement des équipes']);
    }

    /**
     * Affiche la page de rencontre des deux equipes et mot de passe
     */
    public function openEndText()
    {
        $this->logRedirection('openendTextView');
        $this->requireAuth();
        ViewHandler::show('endTextView', ['pageTitle' => 'Le coffre']);
    }


}
