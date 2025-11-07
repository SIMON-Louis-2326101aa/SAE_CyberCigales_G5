<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\ViewHandler;

class RedirectionController
{
    private function logRedirection(string $view): void
    {
        if (function_exists('log_console')) {
            log_console("Redirection vers $view", 'info'); // ℹ️
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

//        // petit contrôle optionnel : on bloque si l’utilisateur n’est pas connecté
//        if (empty($_SESSION['user'])) {
//            if (function_exists('log_console')) {
//                log_console('Tentative d’accès à openAccount sans session', 'error'); // ❌
//            }
//            header('Location: /public/?controller=redirection&action=openFormConnection');
//            exit;
//        }

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
        ViewHandler::show('aboutView', ['pageTitle' => 'À Propos']);
    }

    /* Affiche le plan du Site */
    public function openSiteMap()
    {
        ViewHandler::show('siteMapView', ['pageTitle' => 'Plan du site']);
    }

    /**
     * Affiche la page d'introduction de la lettre de mission
     */
    public function openLetterIntro()
    {
        $this->logRedirection('letterIntroView');
        ViewHandler::show('letterIntroView', ['pageTitle' => 'Introduction']);
    }
}
