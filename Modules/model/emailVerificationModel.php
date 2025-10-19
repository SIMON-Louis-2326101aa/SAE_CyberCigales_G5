<?php

require_once __DIR__ . '/database.php';

class emailVerificationModel extends database
{
    public function __construct()
    {
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS email_verification_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            code VARCHAR(10) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->getBdd()->exec($sql);
        
        // Table pour stocker les inscriptions en attente de vérification
        $sql2 = "CREATE TABLE IF NOT EXISTS pending_registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->getBdd()->exec($sql2);
    }

    public function generateAndStoreCode(string $email, int $ttlMinutes = 10): string
    {
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $ttl = max(1, min(60, (int)$ttlMinutes));

        // Utiliser l'horloge de la base (NOW()) pour éviter les décalages PHP/MySQL
        $sql = "INSERT INTO email_verification_codes (email, code, expires_at, created_at)
                VALUES (:email, :code, DATE_ADD(NOW(), INTERVAL {$ttl} MINUTE), NOW())";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'code' => $code,
        ]);

        return $code;
    }

    public function validateCode(string $email, string $code): bool
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT id FROM email_verification_codes WHERE email = :email AND code = :code AND expires_at >= NOW() ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([
            'email' => $email,
            'code' => $code,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($row);
    }
    
    // Vérifier le statut détaillé du code
    public function checkCodeStatus(string $email, string $code): array
    {
        // Vérifier si le code existe (même expiré)
        $stmt = $this->getBdd()->prepare(
            'SELECT expires_at FROM email_verification_codes WHERE email = :email AND code = :code ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([
            'email' => $email,
            'code' => $code,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            // Le code n'existe pas du tout
            return [
                'valid' => false,
                'reason' => 'incorrect'
            ];
        }
        
        // Vérifier si le code est expiré
        $expiresAt = new DateTime($row['expires_at']);
        $now = new DateTime();
        
        if ($expiresAt < $now) {
            return [
                'valid' => false,
                'reason' => 'expired'
            ];
        }
        
        return [
            'valid' => true,
            'reason' => 'valid'
        ];
    }
    
    // Stocker une inscription en attente de vérification
    public function storePendingRegistration(string $nom, string $prenom, string $email, string $password): bool
    {
        // Supprimer toute inscription en attente existante pour cet email
        $stmt = $this->getBdd()->prepare('DELETE FROM pending_registrations WHERE email = :email');
        $stmt->execute(['email' => $email]);
        
        // Insérer la nouvelle inscription en attente
        $stmt = $this->getBdd()->prepare(
            'INSERT INTO pending_registrations (nom, prenom, email, password, created_at) 
             VALUES (:nom, :prenom, :email, :password, NOW())'
        );
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $password
        ]);
    }
    
    // Récupérer une inscription en attente
    public function getPendingRegistration(string $email): ?array
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT * FROM pending_registrations WHERE email = :email ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    // Créer le compte utilisateur après vérification
    public function createUserAfterVerification(string $email): bool
    {
        $pending = $this->getPendingRegistration($email);
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
}


