<?php

//require_once __DIR__ . '/Database.php';

namespace SAE_CyberCigales_G5\Modules\model;

use PDO;

class EmailVerificationModel extends database
{
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

    // Vérifier le statut détaillé du code
    public function checkCodeStatus(string $email, string $code): array
    {
        // 1. Vérifier si le code existe ET est VALIDE (en utilisant l'horloge de la DB)
        $stmtValid = $this->getBdd()->prepare(
            'SELECT id FROM email_verification_codes 
          WHERE email = :email 
            AND code = :code 
            AND expires_at >= NOW() 
          ORDER BY id DESC LIMIT 1'
        );
        $stmtValid->execute([
            'email' => $email,
            'code' => $code,
        ]);

        if ($stmtValid->fetch(PDO::FETCH_ASSOC)) {
            // Le code est trouvé et non expiré
            return [
                'valid' => true,
                'reason' => 'valid'
            ];
        }

        // 2. Le code n'est pas valide (soit incorrect, soit expiré). Vérifier s'il existe du tout.
        $stmtExists = $this->getBdd()->prepare(
            'SELECT id FROM email_verification_codes WHERE email = :email AND code = :code ORDER BY id DESC LIMIT 1'
        );
        $stmtExists->execute([
            'email' => $email,
            'code' => $code,
        ]);

        if ($stmtExists->fetch(PDO::FETCH_ASSOC)) {
            // Il existe, mais la première requête a échoué -> il est EXPIRE
            return [
                'valid' => false,
                'reason' => 'expired'
            ];
        }

        // 3. Il n'existe pas du tout
        return [
            'valid' => false,
            'reason' => 'incorrect'
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

    public function getAllPendingRegistrations(): array
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT * FROM pending_registrations ORDER BY id ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingRegistrationById(int $id): ?array
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT * FROM pending_registrations WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function deletePendingRegistrationById(int $id): bool
    {
        $stmt = $this->getBdd()->prepare(
            'DELETE FROM pending_registrations WHERE id = :id'
        );
        return $stmt->execute(['id' => $id]);
    }
}