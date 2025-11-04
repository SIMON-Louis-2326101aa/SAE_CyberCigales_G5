<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../../includes/connectionDB.php';
require_once __DIR__ . '/../model/emailVerificationModel.php';

class userModel extends database
{
    private connectionDB $db;
    private $eModel;

    public function __construct()
    {
        // On initialise simplement la connexion de la classe parente
        $this->getBdd();

        if (function_exists('log_console')) {
            log_console('userModel initialisé', 'ok'); // ✅
        }
        $this->db = connectionDB::getInstance();
        $this->eModel = new emailVerificationModel();
    }

    // ANCIEN REGISTER HASH :
//$hash = password_hash($password, PASSWORD_DEFAULT);
//
//$success = $stmt->execute([
//'nom' => $nom,
//'prenom' => $prenom,
//'email' => $email,
//'password' => $hash
//]);

    // Créer le compte utilisateur après vérification
    public function createUserAfterVerification(string $email): bool
    {
        $pending = $this->eModel->getPendingRegistration($email);
        if (!$pending) {
            return false;
        }

        // Créer l'utilisateur avec email vérifié
        $stmt = $this->getBdd()->prepare(
            'INSERT INTO users (nom, prenom, email, password, email_verified, created_at) 
             VALUES (:nom, :prenom, :email, :password, TRUE, NOW())'
        );

        $success = $stmt->execute([
            'nom' => $pending['nom'],
            'prenom' => $pending['prenom'],
            'email' => $pending['email'],
            'password' => $pending['password']
        ]);

        if ($success) {
            // Supprimer l'inscription en attente
            $stmt = $this->getBdd()->prepare('DELETE FROM pending_registrations WHERE email = :email');
            $stmt->execute(['email' => $email]);
        }

        return $success;
    }
    public function findByEmail($email): bool
    {
        $stmt = $this->getBdd()->prepare("SELECT COUNT(*) as count FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int)$result['count']) > 0;
    }

    public function authenticate($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if (function_exists('log_console')) {
                log_console("Authentification réussie : $email", 'ok'); // ✅
            }
            return $user;
        }

        if (function_exists('log_console')) {
            log_console("Échec d’authentification : $email", 'warn'); // ⚠️
        }

        return null;
    }

    public function emailExists(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function changePwd(string $newPassword, string $email): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :newPassword WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        return $stmt->execute([
            'newPassword' => $hashedPassword,
            'email' => $email
        ]);
    }

    public function delete(string $email): bool
    {
        $sql = "DELETE FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        $success = $stmt->execute(['email' => $email]);

        if ($success && function_exists('log_console')) {
            log_console("Utilisateur supprimé : $email", 'file'); // 📄
        }

        return $success;
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

    //Table users
    public function getAllUsers(): array
    {
        return $this->db->getAll('users');
    }

    public function updateUser(int $id, string $nom, string $prenom, string $email): bool
    {
        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email
        ];
        $where = ['id' => $id];
        return $this->db->update('users', $data, $where) > 0;
    }

    public function getUserById(int $id): ?array
    {
        $users = $this->db->getAll('users', ['id' => $id], 1);
        return $users[0] ?? null;
    }
}