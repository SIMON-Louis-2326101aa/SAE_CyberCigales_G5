<?php

namespace SAE_CyberCigales_G5\Modules\model;

use PDO;
use SAE_CyberCigales_G5\includes\ConnectionDB;

/**
 * Modèle de vérification d'email
 *
 * Gère la génération, le stockage et la vérification des codes de vérification d'email,
 * ainsi que les inscriptions en attente de vérification.
 */
class EmailVerificationModel extends Database
{
    private ConnectionDB $db;

    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    public function __construct()
    {
        $this->db = ConnectionDB::getInstance();

        self::log('EmailVerificationModel initialisé', 'ok');
    }

    /**
     * Génère un code de vérification à 6 chiffres
     */
    public static function generateCode(): string
    {
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        self::log('Code de vérification généré', 'file');

        return $code;
    }

    /**
     * Valide et limite le TTL entre 1 et 60 minutes
     */
    public static function validateTTL(int $ttlMinutes): int
    {
        $ttl = max(1, min(60, $ttlMinutes));

        self::log('TTL de vérification validé', 'file', [
            'requested_ttl' => $ttlMinutes,
            'validated_ttl' => $ttl,
        ]);

        return $ttl;
    }

    /**
     * Génère et stocke un code de vérification à 6 chiffres
     */
    public function generateAndStoreCode(string $email, int $ttlMinutes = 10): string
    {
        $code = self::generateCode();
        $ttl = self::validateTTL($ttlMinutes);

        $sql = "INSERT INTO email_verification_codes (email, code, expires_at, created_at)
                VALUES (:email, :code, DATE_ADD(NOW(), INTERVAL {$ttl} MINUTE), NOW())";

        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'code' => $code,
        ]);

        self::log('Code de vérification stocké', 'ok', [
            'email' => $email,
            'ttl_minutes' => $ttl,
        ]);

        return $code;
    }

    /**
     * Vérifie le statut d'un code de vérification
     */
    public function checkCodeStatus(string $email, string $code): array
    {
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
            self::log('Code de vérification valide', 'ok', [
                'email' => $email,
            ]);

            return [
                'valid' => true,
                'reason' => 'valid'
            ];
        }

        $stmtExists = $this->getBdd()->prepare(
            'SELECT id FROM email_verification_codes 
             WHERE email = :email 
               AND code = :code 
             ORDER BY id DESC LIMIT 1'
        );

        $stmtExists->execute([
            'email' => $email,
            'code' => $code,
        ]);

        if ($stmtExists->fetch(PDO::FETCH_ASSOC)) {
            self::log('Code de vérification expiré', 'warn', [
                'email' => $email,
            ]);

            return [
                'valid' => false,
                'reason' => 'expired'
            ];
        }

        self::log('Code de vérification incorrect', 'warn', [
            'email' => $email,
        ]);

        return [
            'valid' => false,
            'reason' => 'incorrect'
        ];
    }

    /**
     * Stocke une inscription en attente de vérification
     */
    public function storePendingRegistration(string $nom, string $prenom, string $email, string $password): bool
    {
        $stmt = $this->getBdd()->prepare('DELETE FROM pending_registrations WHERE email = :email');
        $stmt->execute(['email' => $email]);

        $stmt = $this->getBdd()->prepare(
            'INSERT INTO pending_registrations (nom, prenom, email, password, created_at) 
             VALUES (:nom, :prenom, :email, :password, NOW())'
        );

        $result = $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $password
        ]);

        self::log('Inscription en attente stockée', $result ? 'ok' : 'error', [
            'email' => $email,
        ]);

        return $result;
    }

    /**
     * Récupérer une inscription en attente
     */
    public function getPendingRegistration(string $email): ?array
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT * FROM pending_registrations WHERE email = :email ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        self::log('Recherche inscription en attente effectuée', 'file', [
            'email' => $email,
            'found' => $result !== null,
        ]);

        return $result;
    }

    public function deleteCode(string $code): bool
    {
        $deleted = $this->db->delete('email_verification_codes', ['code' => $code]) > 0;

        self::log('Suppression code de vérification', $deleted ? 'ok' : 'warn', [
            'deleted' => $deleted,
        ]);

        return $deleted;
    }

    public function deleteExpiredCodes(): void
    {
        $stmt = $this->getBdd()->prepare("DELETE FROM email_verification_codes WHERE expires_at <= NOW()");
        $stmt->execute();

        self::log('Suppression des codes expirés exécutée', 'file', [
            'deleted_count' => $stmt->rowCount(),
        ]);
    }
}
