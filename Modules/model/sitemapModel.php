<?php

class sitemapModel
{
    public function getLinks(): array
    {
        // Liens principaux du site (ajoutez/ajustez selon les pages existantes)
        return [
            ['label' => 'Accueil', 'href' => 'index.php?controller=homepage&action=openHomepage'],
            ['label' => 'Connexion', 'href' => 'index.php?controller=formConnection&action=login'],
            ['label' => 'Inscription', 'href' => 'index.php?controller=formRegister&action=register'],
            ['label' => 'Compte', 'href' => 'index.php?controller=account&action=account'],
            ['label' => 'Mot de passe oublié', 'href' => 'index.php?controller=forgotPwd&action=forgot'],
            ['label' => 'Mentions Légales', 'href' => 'index.php?controller=legalMention&action=legal'],
        ];
    }
}


