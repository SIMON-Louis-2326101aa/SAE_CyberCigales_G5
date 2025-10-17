<?php
/**
* Class homepageModel
*
* Cette classe représente le modèle pour la page d'accueil.
* Elle hérite de la classe `database` afin de bénéficier de la connexion à la base de données.
* Le modèle peut être utilisé pour interagir avec la base de données concernant la page d'accueil.
*/
class homepageModel extends database {
/**
* homePageModel constructor.
*
* Le constructeur de cette classe appelle la méthode `getBdd()` de la classe parente `database`
* afin d'établir une connexion à la base de données. Cette connexion est ensuite disponible
* pour toute interaction avec la base de données dans ce modèle.
*/
    public function __construct() {
// Appelle la méthode `getBdd()` pour obtenir la connexion à la base de données.
    $this->getBdd();
    }
}
?>