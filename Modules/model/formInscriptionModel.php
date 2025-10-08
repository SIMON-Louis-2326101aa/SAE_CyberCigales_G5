<?php
require_once __DIR__ . '/database.php';

class formInscriptionModel extends database
{
    public function register($nom, $prenom, $email, $password) {
        $bdd = $this->getBdd();

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $bdd->prepare("INSERT INTO users (nom, prenom, email, password)  VALUES (:nom, :prenom, :email, :password) ");
        return $stmt->execute([ 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'password' => $hash]);
    }

    public function findByEmail($email) {
        $bdd = $this->getBdd();

        $stmt = $bdd->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
}