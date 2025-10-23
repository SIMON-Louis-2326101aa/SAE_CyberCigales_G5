<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../../includes/connectionDB.php';

class loginAttemptModel extends database
{
    private connectionDB $db;

    public function __construct()
    {
        $this->db = connectionDB::getInstance();
    }

    /**
     * Enregistre une tentative de connexion échouée
     * @param string $email L'email utilisé pour la tentative
     * @param string $ip L'adresse IP de l'utilisateur
     */
    public function recordFailedAttempt(string $email, string $ip): void
    {
        $sql = "INSERT INTO login_attempts (email, ip_address, attempted_at) VALUES (:email, :ip, NOW())";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'ip' => $ip
        ]);
    }

    /**
     * Compte le nombre de tentatives échouées pour un email dans les dernières minutes
     * @param string $email L'email à vérifier
     * @param int $minutes Nombre de minutes à considérer (défaut: 15)
     * @return int Nombre de tentatives échouées
     */
    public function getFailedAttemptsCount(string $email, int $minutes = 15): int
    {
        $sql = "SELECT COUNT(*) FROM login_attempts 
                WHERE email = :email 
                AND attempted_at > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'minutes' => $minutes
        ]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Compte le nombre de tentatives échouées pour une IP dans les dernières minutes
     * @param string $ip L'adresse IP à vérifier
     * @param int $minutes Nombre de minutes à considérer (défaut: 15)
     * @return int Nombre de tentatives échouées
     */
    public function getFailedAttemptsCountByIP(string $ip, int $minutes = 15): int
    {
        $sql = "SELECT COUNT(*) FROM login_attempts 
                WHERE ip_address = :ip 
                AND attempted_at > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute([
            'ip' => $ip,
            'minutes' => $minutes
        ]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Vérifie si un compte est temporairement bloqué
     * @param string $email L'email à vérifier
     * @return array ['blocked' => bool, 'remaining_time' => int, 'attempts' => int]
     */
    public function isAccountBlocked(string $email): array
    {
        $attempts = $this->getFailedAttemptsCount($email);
        $maxAttempts = 5; // Maximum 5 tentatives
        $blockDuration = 15; // Bloqué pendant 15 minutes

        if ($attempts >= $maxAttempts) {
            // Récupérer la dernière tentative pour calculer le temps restant
            $sql = "SELECT attempted_at FROM login_attempts 
                    WHERE email = :email 
                    ORDER BY attempted_at DESC 
                    LIMIT 1";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute(['email' => $email]);
            $lastAttempt = $stmt->fetchColumn();

            if ($lastAttempt) {
                $lastAttemptTime = strtotime($lastAttempt);
                $currentTime = time();
                $timeElapsed = $currentTime - $lastAttemptTime;
                $remainingTime = max(0, ($blockDuration * 60) - $timeElapsed);

                return [
                    'blocked' => $remainingTime > 0,
                    'remaining_time' => $remainingTime,
                    'attempts' => $attempts
                ];
            }
        }

        return [
            'blocked' => false,
            'remaining_time' => 0,
            'attempts' => $attempts
        ];
    }

    /**
     * Vérifie si une IP est temporairement bloquée
     * @param string $ip L'adresse IP à vérifier
     * @return array ['blocked' => bool, 'remaining_time' => int, 'attempts' => int]
     */
    public function isIPBlocked(string $ip): array
    {
        $attempts = $this->getFailedAttemptsCountByIP($ip);
        $maxAttempts = 10; // Maximum 10 tentatives par IP
        $blockDuration = 30; // Bloqué pendant 30 minutes

        if ($attempts >= $maxAttempts) {
            // Récupérer la dernière tentative pour calculer le temps restant
            $sql = "SELECT attempted_at FROM login_attempts 
                    WHERE ip_address = :ip 
                    ORDER BY attempted_at DESC 
                    LIMIT 1";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute(['ip' => $ip]);
            $lastAttempt = $stmt->fetchColumn();

            if ($lastAttempt) {
                $lastAttemptTime = strtotime($lastAttempt);
                $currentTime = time();
                $timeElapsed = $currentTime - $lastAttemptTime;
                $remainingTime = max(0, ($blockDuration * 60) - $timeElapsed);

                return [
                    'blocked' => $remainingTime > 0,
                    'remaining_time' => $remainingTime,
                    'attempts' => $attempts
                ];
            }
        }

        return [
            'blocked' => false,
            'remaining_time' => 0,
            'attempts' => $attempts
        ];
    }

    /**
     * Nettoie les anciennes tentatives de connexion (plus de 24h)
     */
    public function cleanupOldAttempts(): void
    {
        $sql = "DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute();
    }

    /**
     * Supprime les tentatives d'un email après une connexion réussie
     * @param string $email L'email de l'utilisateur connecté
     */
    public function clearFailedAttempts(string $email): void
    {
        $sql = "DELETE FROM login_attempts WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute(['email' => $email]);
    }
}
