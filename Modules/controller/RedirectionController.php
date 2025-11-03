<?php

namespace SAE_CyberCigales_G5\Modules\controller;

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
        viewHandler::show('homepageView', ['pageTitle' => 'Accueil']);
    }

    public function openLegal()
    {
        $this->logRedirection('legalMentionView');
        viewHandler::show('legalMentionView', ['pageTitle' => 'Mentions Légales']);
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

        viewHandler::show('accountView', ['pageTitle' => 'Compte']);
    }

    public function openFormConnection()
    {
        $this->logRedirection('formConnectionView');
        viewHandler::show('formConnectionView', ['pageTitle' => 'Connexion']);
    }

    public function openFormRegister()
    {
        $this->logRedirection('formRegisterView');
        viewHandler::show('formRegisterView', ['pageTitle' => 'S\'inscrire']);
    }

    public function openForgotPwd()
    {
        $this->logRedirection('forgotPwdView');
        viewHandler::show('forgotPwdView', ['pageTitle' => 'Mot de passe oublié']);
    }

    public function openChangePwd()
    {
        $this->logRedirection('changePwdView');
        viewHandler::show('changePwdView', ['pageTitle' => 'Changement de mot de passe', 'token' => $_GET['token']
            ?? '']);
    }

    public function openEmailVerification()
    {
        $this->logRedirection('emailVerificationView');
        viewHandler::show('emailVerificationView', ['pageTitle' => 'Vérification de l\'e-mail','email' => $_GET['email']
            ?? '']);
    }

    /**
     * Affiche la page "À propos"
     */
    public function openAbout()
    {
        viewHandler::show('aboutView', ['pageTitle' => 'À Propos']);
    }

    /* Affiche le plan du Site */
    public function openSiteMap()
    {
        viewHandler::show('siteMapView', ['pageTitle' => 'Plan du site']);
    }
}
