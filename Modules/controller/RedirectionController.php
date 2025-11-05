<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\ViewHandler;

/**
 * Contrôleur de redirection et affichage des vues
 * 
 * Gère l'affichage de toutes les pages statiques et vues du site.
 * Toutes les méthodes publiques suivent le pattern : openNomDeLaPage()
 * 
 * @package SAE_CyberCigales_G5\Modules\controller
 * @author Équipe CyberCigales
 */
class RedirectionController
{
    /**
     * Log une redirection vers une vue
     * 
     * @param string $view Nom de la vue vers laquelle on redirige
     * @return void
     */
    private function logRedirection(string $view): void
    {
        if (function_exists('log_console')) {
            log_console("Redirection vers $view", 'info'); // ℹ️
        }
    }

    /**
     * Affiche la page d'accueil
     * 
     * @return void
     */
    public function openHomepage()
    {
        $this->logRedirection('homepageView');
        ViewHandler::show('homepageView', ['pageTitle' => 'Accueil']);
    }

    /**
     * Affiche la page des mentions légales
     * 
     * @return void
     */
    public function openLegal()
    {
        $this->logRedirection('legalMentionView');
        ViewHandler::show('legalMentionView', ['pageTitle' => 'Mentions Légales']);
    }

    /**
     * Affiche la page de gestion du compte utilisateur
     * 
     * @return void
     */
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

    /**
     * Affiche le formulaire de connexion
     * 
     * @return void
     */
    public function openFormConnection()
    {
        $this->logRedirection('formConnectionView');
        ViewHandler::show('formConnectionView', ['pageTitle' => 'Connexion']);
    }

    /**
     * Affiche le formulaire d'inscription
     * 
     * @return void
     */
    public function openFormRegister()
    {
        $this->logRedirection('formRegisterView');
        ViewHandler::show('formRegisterView', ['pageTitle' => 'S\'inscrire']);
    }

    /**
     * Affiche le formulaire de mot de passe oublié
     * 
     * @return void
     */
    public function openForgotPwd()
    {
        $this->logRedirection('forgotPwdView');
        ViewHandler::show('forgotPwdView', ['pageTitle' => 'Mot de passe oublié']);
    }

    /**
     * Affiche le formulaire de changement de mot de passe
     * 
     * Nécessite un token valide passé en paramètre GET.
     * 
     * @return void
     */
    public function openChangePwd()
    {
        $this->logRedirection('changePwdView');
        ViewHandler::show('changePwdView', ['pageTitle' => 'Changement de mot de passe', 'token' => $_GET['token']
            ?? '']);
    }

    /**
     * Affiche la page de vérification d'email
     * 
     * Nécessite un email passé en paramètre GET.
     * 
     * @return void
     */
    public function openEmailVerification()
    {
        $this->logRedirection('emailVerificationView');
        ViewHandler::show('emailVerificationView', ['pageTitle' => 'Vérification de l\'e-mail','email' => $_GET['email']
            ?? '']);
    }

    /**
     * Affiche la page "À propos"
     * 
     * @return void
     */
    public function openAbout()
    {
        ViewHandler::show('aboutView', ['pageTitle' => 'À Propos']);
    }

    /**
     * Affiche le plan du site
     * 
     * @return void
     */
    public function openSiteMap()
    {
        ViewHandler::show('siteMapView', ['pageTitle' => 'Plan du site']);
    }
}
