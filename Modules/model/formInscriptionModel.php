<?php
require_once __DIR__ . '/database.php';

class formInscriptionModel extends database
{
    public function register(string $nom, string $prenom, string $email, string $password): bool
    {
        $sql = "INSERT INTO users (nom, prenom, email, password) VALUES (:nom, :prenom, :email, :password)";
        $stmt = $this->getBdd()->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $password
        ]);

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $bdd->prepare("INSERT INTO users (nom, prenom, email, password)  VALUES (:nom, :prenom, :email, :password) ");
        return $stmt->execute([ 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'password' => $hash]);
    }

    public function findByEmail($email): bool
    {
        $bdd = $this->getBdd();

        $sql = "SELECT COUNT(*) FROM users WHERE Mail = :mail";
        $stmt = $bdd->prepare($sql);
        $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() > 0;
    }
}