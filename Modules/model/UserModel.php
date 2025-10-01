<?php
require_once __DIR__ . '/../../includes/constant.php';

class UserModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function register($nom, $prenom, $email, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (nom, prenom, email, password)  VALUES (:nom, :prenom, :email, :password) ");
        return $stmt->execute([ 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'password' => $hash]);
    }


    public function login($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
