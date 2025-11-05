<?php

declare(strict_types=1);

//require_once __DIR__ . '/Database.php';
//require_once __DIR__ . '/../../includes/ConnectionDB.php';

namespace SAE_CyberCigales_G5\Modules\model;

/**
 * Modèle de réinitialisation de mot de passe
 * 
 * Gère la génération, le stockage et la validation des tokens de réinitialisation
 * de mot de passe. Les tokens expirent après un délai défini (par défaut 60 minutes).
 * 
 * @package SAE_CyberCigales_G5\Modules\model
 * @author Équipe CyberCigales
 */
class PasswordResetModel extends Database
{
    /**
     * Constructeur
     * 
     * Initialise la connexion à la base de données.
     */
    public function __construct()
    {
        $this->getBdd();
    }

    /**
     * Crée et stocke un token de réinitialisation pour un email
     * 
     * Génère un token sécurisé unique et le stocke en base de données
     * avec une date d'expiration. Supprime tout token existant pour cet email.
     * 
     * @param string $email Email de l'utilisateur
     * @param int $ttlMinutes Durée de validité du token en minutes (par défaut 60)
     * @return string|false Le token généré ou false si l'email n'existe pas
     */
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

        $ins = $this->getBdd()->
        prepare('INSERT INTO password_reset_tokens (user_id, token, expires_at, used, created_at) 
VALUES (?, ?, ?, 0, NOW())');
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
