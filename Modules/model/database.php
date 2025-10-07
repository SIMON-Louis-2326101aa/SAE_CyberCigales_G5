<?php

/**
 * Classe abstraite pour la gestion de la connexion à la base de données.
 * Cette classe permet de charger les variables d'environnement,
 * de se connecter à la base de données et de récupérer la connexion.
 */
abstract class database
{
    // Instance statique de la connexion à la base de données
    private static $_bdd;

    /**
     * Charge les variables d'environnement depuis le fichier .env.
     * Ce fichier contient les informations de connexion à la base de données.
     * Les variables sont chargées dans la variable globale $_ENV.
     *
     * @throws Exception Si le fichier .env est introuvable.
     */
    private static function loadEnv()
    {
        // Chemin vers le fichier .env
        $envFile = __DIR__ . '/../../.env';

        // Vérifier si le fichier .env existe
        if (!file_exists($envFile)) {
            die("Erreur 404");
        }

        // Lire chaque ligne du fichier .env
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Si la ligne contient un signe égal, elle est considérée comme une variable d'environnement
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }

    /**
     * Instancie la connexion à la base de données à l'aide des variables d'environnement.
     * Les informations de connexion sont récupérées depuis $_ENV.
     *
     * @throws PDOException Si la connexion à la base de données échoue.
     */
    private static function setBdd()
    {
        // Charger les variables d'environnement
        self::loadEnv();

        // Récupérer les valeurs des variables d'environnement
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        try {
            // Créer la connexion PDO à la base de données
            self::$_bdd = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            self::$_bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (PDOException $e) {
            // En cas d'erreur, afficher un message d'erreur et stopper l'exécution
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Récupère la connexion à la base de données.
     * Si la connexion n'a pas encore été établie, elle est créée en appelant la méthode setBdd().
     *
     * @return PDO La connexion à la base de données.
     */
    public function getBdd()
    {
        // Si la connexion n'existe pas encore, la créer
        if (self::$_bdd == null) {
            self::setBdd();
        }
        return self::$_bdd;
    }
}
?>