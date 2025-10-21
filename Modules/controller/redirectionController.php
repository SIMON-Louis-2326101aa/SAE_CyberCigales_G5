<?php

class redirectionController
{
    public function openHomepage()
    {
        viewHandler::show('/homepageView', ['pageTitle' => 'Accueil']);
    }
    public function openLegal()
    {
        viewHandler::show('/legalMentionView', ['pageTitle' => 'Mentions Légales']);
    }
    public function openAccount()
    {
        viewHandler::show('/accountView', ['pageTitle' => 'Compte']);
    }
    public function openFormConnection()
    {
        viewHandler::show('/formConnectionView', ['pageTitle' => 'Connexion']);
    }
    public function openFormRegister()
    {
        viewHandler::show('/formRegisterView', ['pageTitle' => 'S\'inscrire']);
    }
    public function openForgotPwd()
    {
        viewHandler::show('forgotPwdView', ['pageTitle' => 'Mot de passe oublié']);
    }
    public function openChangePwd()
    {
        viewHandler::show('/changePwdView', ['pageTitle' => 'Changement de mot de passe']);
    }
}