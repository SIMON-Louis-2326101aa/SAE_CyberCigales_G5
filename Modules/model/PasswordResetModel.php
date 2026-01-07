<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\model;

use PDO;

class PasswordResetModel extends Database
{
    // Crée et stocke un token pour l'utilisateur identifié par l'email
    public function createTokenForEmail(string $email, int $ttlMinutes = 60)
    {
        $stmt = $this->getBdd()->prepare(
            'SELECT id FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));

        $stmt = $this->getBdd()->prepare(
            'INSERT INTO password_reset_tokens (user_id, token, expires_at, used, created_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE), 0, NOW())'
        );
        $stmt->execute([$user['id'], $token, $ttlMinutes]);

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
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function markTokenUsed(string $token)
    {
        $stmt = $this->getBdd()->prepare(
            'UPDATE password_reset_tokens SET used = 1 WHERE token = ?'
        );
        return $stmt->execute([$token]);
    }

    public function purgeExpired()
    {
        $stmt = $this->getBdd()->prepare(
            'DELETE FROM password_reset_tokens WHERE expires_at < NOW()'
        );
        return $stmt->execute();
    }
}
