<?php
/**
 * Contrôleur de redirection
 * 
 * Ce contrôleur gère l'affichage de toutes les pages (vues) du site.
 * Il centralise les redirections vers les différentes vues sans logique métier.
 * 
 * Chaque méthode affiche une vue spécifique en passant le titre de la page.
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

class redirectionController
{
    /**
     * Affiche la page d'accueil
     * 
     * @return void
     */
    public function openHomepage()
    {
        viewHandler::show('/homepageView', ['pageTitle' => 'Accueil']);
    }
    
    /**
     * Affiche la page des mentions légales
     * 
     * @return void
     */
    public function openLegal()
    {
        viewHandler::show('/legalMentionView', ['pageTitle' => 'Mentions Légales']);
    }
    
    /**
     * Affiche la page de compte utilisateur
     * 
     * @return void
     */
    public function openAccount()
    {
        viewHandler::show('/accountView', ['pageTitle' => 'Compte']);
    }
    
    /**
     * Affiche le formulaire de connexion
     * 
     * @return void
     */
    public function openFormConnection()
    {
        viewHandler::show('/formConnectionView', ['pageTitle' => 'Connexion']);
    }
    
    /**
     * Affiche le formulaire d'inscription
     * 
     * @return void
     */
    public function openFormRegister()
    {
        viewHandler::show('/formRegisterView', ['pageTitle' => 'S\'inscrire']);
    }
    
    /**
     * Affiche le formulaire "mot de passe oublié"
     * 
     * @return void
     */
    public function openForgotPwd()
    {
        viewHandler::show('forgotPwdView', ['pageTitle' => 'Mot de passe oublié']);
    }
    
    /**
     * Affiche le formulaire de changement de mot de passe
     * 
     * @return void
     */
    public function openChangePwd()
    {
        viewHandler::show('/changePwdView', ['pageTitle' => 'Changement de mot de passe']);
    }
}