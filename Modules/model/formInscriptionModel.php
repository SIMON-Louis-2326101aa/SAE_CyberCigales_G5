<?php
require_once __DIR__ . '/database.php';

class formInscriptionModel extends database
{
    public function register(string $nom, string $prenom, string $email, string $password): bool
    {
        $sql = "INSERT INTO users (nom, prenom, email, password) VALUES (:nom, :prenom, :email, :password)";
        $stmt = $this->getBdd()->prepare($sql);
        $hash = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $hash
        ]);
    }

    public function findByEmail($email): bool
    {
        $bdd = $this->getBdd();
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}