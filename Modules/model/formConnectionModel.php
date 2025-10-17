<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../../includes/connectionDB.php';

class formConnectionModel extends database
{
    private connectionDB $db;
    public function __construct() {
        $this->db = connectionDB::getInstance();
    }
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
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