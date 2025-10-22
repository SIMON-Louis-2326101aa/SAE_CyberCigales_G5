<?php
/**
 * Modèle de la page d'accueil
 * 
 * Ce modèle gère les données et la logique métier de la page d'accueil.
 * Il hérite de la classe `database` pour bénéficier de la connexion à la base de données.
 * 
 * Actuellement, ce modèle n'a pas de méthodes spécifiques mais peut être étendu
 * pour gérer des données dynamiques sur la page d'accueil (statistiques, contenu, etc.)
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

class pageModel extends database 
{
    /**
     * Constructeur - initialise la connexion à la base de données
     * 
     * Appelle getBdd() de la classe parente pour établir la connexion.
     * Cette connexion sera disponible pour toutes les méthodes de ce modèle.
     */
    public function __construct() 
    {
        // Initialise la connexion à la base de données via la classe parente
        $this->getBdd();
    }
}