<?php
require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../../includes/connexionDB.php';
class formConnectionModel extends database
{
    private connexionDB $db;
    public function __construct() {
        $this->db = connexionDB::getInstance();
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
}