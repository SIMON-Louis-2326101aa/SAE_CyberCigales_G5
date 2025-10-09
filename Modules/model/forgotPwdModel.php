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
    public function emailExists(string $email): bool
    {
        $bdd = $this->getBdd();
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
}