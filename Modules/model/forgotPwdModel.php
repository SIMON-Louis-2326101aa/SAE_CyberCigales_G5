<?php

require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../../includes/connexionDB.php';
class forgotPwdModel extends database
{
    private connexionDB $db;

    public function __construct() {
        // Supposons que connexionDB est votre classe de singleton/connexion
        $this->db = connexionDB::getInstance();
    }

    /**
     * Vérifie si un utilisateur avec l'email donné existe.
     * @param string $email L'email à vérifier.
     * @return bool Vrai si l'email existe, faux sinon.
     */
    public function emailExists(string $email): bool
    {
        $bdd = $this->db->getBdd(); // Assurez-vous que connexionDB a une méthode getBdd()
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);

        // Retourne true si le COUNT est supérieur à 0
        return $stmt->fetchColumn() > 0;
    }

}