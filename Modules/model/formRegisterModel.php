<?php
require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../../includes/connectionDB.php';
class formRegisterModel extends database
{
    private connectionDB $db;
    public function __construct() {
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
        
        // Vérifier si l'email existe déjà dans les utilisateurs
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            return true;
        }
        
        // Vérifier aussi dans les inscriptions en attente
        $sql = "SELECT COUNT(*) as count FROM pending_registrations WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    // Nouvelle méthode pour obtenir le statut de l'email
    public function getEmailStatus($email): array
    {
        $bdd = $this->getBdd();
        
        // Vérifier dans users
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);
        $inUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        // Vérifier dans pending_registrations
        $sql = "SELECT COUNT(*) as count FROM pending_registrations WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);
        $inPending = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        return [
            'exists' => $inUsers || $inPending,
            'verified' => $inUsers,
            'pending' => $inPending
        ];
    }
}