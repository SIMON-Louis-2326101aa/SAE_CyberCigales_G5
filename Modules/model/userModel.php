<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../../includes/connectionDB.php';

class userModel extends database
{
    public function __construct()
    {
        // On initialise simplement la connexion de la classe parente
        $this->getBdd();

        if (function_exists('log_console')) {
            log_console('userModel initialisé', 'ok'); // ✅
        }
    }

    public function register(string $nom, string $prenom, string $email, string $password): bool
    {
        try {
            $sql = "INSERT INTO users (nom, prenom, email, password) VALUES (:nom, :prenom, :email, :password)";
            $stmt = $this->getBdd()->prepare($sql);
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $success = $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $hash
            ]);

            if ($success && function_exists('log_console')) {
                log_console("Nouvel utilisateur créé : $email", 'ok'); // ✅
            }

            return $success;
        } catch (Throwable $e) {
            if (function_exists('log_console')) {
                log_console("Erreur register($email) : " . $e->getMessage(), 'error'); // ❌
            }
            return false;
        }
    }

    public function findByEmail($email): bool
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
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
}
