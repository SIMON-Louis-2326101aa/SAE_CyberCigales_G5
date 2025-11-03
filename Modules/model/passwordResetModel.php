<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../../includes/ConnectionDB.php';
class passwordResetModel extends database
{
    public function __construct()
    {
        $this->getBdd();
    }


    // Crée et stocke un token pour l'utilisateur identifié par l'email. Retourne le token ou false.
    public function createTokenForEmail(string $email, int $ttlMinutes = 60)
    {
        $stmt = $this->getBdd()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + $ttlMinutes * 60);

        $ins = $this->getBdd()->prepare('INSERT INTO password_reset_tokens (user_id, token, expires_at, used, created_at) VALUES (?, ?, ?, 0, NOW())');
        $ins->execute([$user['id'], $token, $expires]);

        return $token;
    }

    // Retourne la ligne du token si valide (non utilisé et non expiré) avec l'email de l'utilisateur, ou false.
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
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: false;
    }

    // Marque le token comme utilisé
    public function markTokenUsed(string $token)
    {
        $stmt = $this->getBdd()->prepare('UPDATE password_reset_tokens SET used = 1 WHERE token = ?');
        return $stmt->execute([$token]);
    }

    // Optionnel : purge des tokens expirés
    public function purgeExpired()
    {
        $stmt = $this->getBdd()->prepare('DELETE FROM password_reset_tokens WHERE expires_at < NOW()');
        return $stmt->execute();
    }
}