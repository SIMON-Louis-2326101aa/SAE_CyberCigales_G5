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
        // petit contrôle optionnel : on bloque si l’utilisateur n’est pas connecté
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
}
