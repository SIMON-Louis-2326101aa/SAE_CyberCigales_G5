<?php
require_once __DIR__ . '/../../includes/constant.php';
require_once __DIR__ . '/database.php';

class UserModel extends Database{
    private $pdo;

    public function __construct() {
        $this->pdo = $this->getBdd();
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function login($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
