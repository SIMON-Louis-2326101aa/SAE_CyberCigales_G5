<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\model;

use PDO;

class PasswordResetModel extends Database
{
    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    // Crée et stocke un token pour l'utilisateur identifié par l'email
    public function createTokenForEmail(string $email, int $ttlMinutes = 60)
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT id FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            self::log("Password reset demandé pour email inconnu", "warn", [
                "email" => $email
            ]);
            return false;
        }

        $token = bin2hex(random_bytes(32));

        $stmt = $this->getBdd()->prepare(
            'INSERT INTO password_reset_tokens (user_id, token, expires_at, used, created_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE), 0, NOW())'
        );

        $stmt->execute([$user['id'], $token, $ttlMinutes]);

        self::log("Token reset password créé", "ok", [
            "user_id" => $user['id'],
            "email" => $email,
            "ttl_minutes" => $ttlMinutes
        ]);

        return $token;
    }

    public function getValidTokenRow(string $token)
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT prt.*, u.email
             FROM password_reset_tokens prt
             JOIN users u ON u.id = prt.user_id
             WHERE prt.token = ? AND prt.used = 0 AND prt.expires_at >= NOW()
             LIMIT 1'
        );

        $stmt->execute([$token]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            self::log("Token reset invalide ou expiré", "warn", [
                "token_prefix" => substr($token, 0, 8)
            ]);
        } else {
            self::log("Token reset valide récupéré", "file", [
                "user_id" => $row['user_id'],
                "email" => $row['email']
            ]);
        }

        return $row ?: false;
    }

    public function markTokenUsed(string $token)
    {
        $stmt = $this->getBdd()->prepare(
            'UPDATE password_reset_tokens SET used = 1 WHERE token = ?'
        );

        $result = $stmt->execute([$token]);

        self::log("Token reset marqué utilisé", $result ? "ok" : "error", [
            "token_prefix" => substr($token, 0, 8)
        ]);

        return $result;
    }

    public function purgeExpired()
    {
        $stmt = $this->getBdd()->prepare(
            'DELETE FROM password_reset_tokens WHERE expires_at < NOW()'
        );

        $stmt->execute();

        self::log("Tokens reset expirés supprimés", "file", [
            "deleted_count" => $stmt->rowCount()
        ]);

        return true;
    }
}
