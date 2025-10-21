<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../../includes/connectionDB.php';

class userModel extends database
{
    private connectionDB $db;

    public function __construct()
    {
        $this->db = connectionDB::getInstance();
    }
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
    public function authenticate($email, $password)
    {
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
    public function changePwd(string $newPassword, string $email): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $bdd = $this->getBdd();
        $sql = "UPDATE users SET  password = :newPassword WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        return $stmt->execute([
            'newPassword' => $hashedPassword,
            'email' => $email
        ]);
    }
    public function delete(string $email): bool
    {
        $sql = "DELETE FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        return $stmt->execute([
            'email' => $email,
        ]);
    }
}