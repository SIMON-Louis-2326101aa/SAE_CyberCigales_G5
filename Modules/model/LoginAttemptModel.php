<?php

/**
 * Modèle de gestion des tentatives de connexion
 * Utilise les SESSIONS PHP au lieu de la base de données
 * Plus simple, plus rapide, aucune modification de BDD nécessaire
 */

namespace SAE_CyberCigales_G5\Modules\model;

class LoginAttemptModel
{
    private const MAX_ATTEMPTS = 5; // Maximum de tentatives avant premier blocage
    private const INITIAL_BLOCK_DURATION = 1; // Durée initiale de blocage (1 minute à la 4ème tentative)
    private const MAX_BLOCK_DURATION = 60; // Durée maximale de blocage (cap à 1 heure)
    private const SESSION_KEY = 'login_attempts';

    /**
     * Initialise la session si nécessaire
     */
    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    /**
     * Nettoie les tentatives anciennes pour un email ou une IP
     * @param string $key La clé (email ou ip_xxx)
     */
    private function cleanOldAttempts(string $key): void
    {
        $this->initSession();
        if (!isset($_SESSION[self::SESSION_KEY][$key])) {
            return;
        }
        // Utiliser le temps de blocage maximum pour le nettoyage
        $cutoffTime = time() - (self::MAX_BLOCK_DURATION * 60);
        $validAttempts = [];
        foreach ($_SESSION[self::SESSION_KEY][$key]['attempts'] as $timestamp) {
            if ($timestamp > $cutoffTime) {
                $validAttempts[] = $timestamp;
            }
        }
        $_SESSION[self::SESSION_KEY][$key]['attempts'] = $validAttempts;
        $_SESSION[self::SESSION_KEY][$key]['count'] = count($validAttempts);
        // Supprimer l'entrée si plus de tentatives
        if (empty($validAttempts)) {
            unset($_SESSION[self::SESSION_KEY][$key]);
        }
    }

    /**
     * Enregistre une tentative de connexion échouée
     * @param string $email L'email utilisé pour la tentative
     * @param string $ip L'adresse IP
     */
    public function recordFailedAttempt(string $email, string $ip): void
    {
        $this->initSession();
        // Enregistrer pour l'email
        if (!isset($_SESSION[self::SESSION_KEY][$email])) {
            $_SESSION[self::SESSION_KEY][$email] = [
                'count' => 0,
                'attempts' => []
            ];
        }
        $_SESSION[self::SESSION_KEY][$email]['attempts'][] = time();
        $_SESSION[self::SESSION_KEY][$email]['count']++;
        $this->cleanOldAttempts($email);
        // Enregistrer pour l'IP
        $ipKey = 'ip_' . md5($ip);
        if (!isset($_SESSION[self::SESSION_KEY][$ipKey])) {
            $_SESSION[self::SESSION_KEY][$ipKey] = [
                'count' => 0,
                'attempts' => []
            ];
        }
        $_SESSION[self::SESSION_KEY][$ipKey]['attempts'][] = time();
        $_SESSION[self::SESSION_KEY][$ipKey]['count']++;
        $this->cleanOldAttempts($ipKey);
    }

    /**
     * Calcule le temps de blocage en fonction du nombre de tentatives (exponentiel)
     * @param int $attempts Nombre de tentatives échouées
     * @return int Temps de blocage en minutes
     */
    private function calculateBlockDuration(int $attempts): int
    {
        // Pas de blocage pour les 3 premières tentatives
        if ($attempts < 4) {
            return 0;
        }
        // Progression exponentielle :
        // 4ème tentative : 1 minute
        // 5ème tentative : 2 minutes
        // 6ème tentative : 4 minutes
        // 7ème tentative : 8 minutes
        // 8ème tentative : 16 minutes
        // 9ème tentative : 32 minutes
        // 10ème+ tentative : 60 minutes (cap à 1 heure)
        // Calcul exponentiel : 2^(attempts - 4)
        $blockMinutes = self::INITIAL_BLOCK_DURATION * pow(2, $attempts - 4);
        // Cap au maximum (1 heure)
        return min($blockMinutes, self::MAX_BLOCK_DURATION);
    }

    /**
     * Compte le nombre de tentatives échouées pour un email
     * @param string $email L'email à vérifier
     * @return int Nombre de tentatives
     */
    public function getFailedAttemptsCount(string $email): int
    {
        $this->initSession();
        $this->cleanOldAttempts($email);
        if (!isset($_SESSION[self::SESSION_KEY][$email])) {
            return 0;
        }
        return $_SESSION[self::SESSION_KEY][$email]['count'];
    }

    /**
     * Vérifie si un compte est temporairement bloqué
     * @param string $email L'email à vérifier
     * @return array ['blocked' => bool, 'remaining_time' => int, 'attempts' => int, 'block_duration' => int]
     */
    public function isAccountBlocked(string $email): array
    {
        $this->initSession();
        $this->cleanOldAttempts($email);
        $attempts = $this->getFailedAttemptsCount($email);
        // Blocage progressif à partir de 4 tentatives
        if ($attempts >= 4) {
            // Calculer le temps de blocage exponentiel
            $blockDuration = $this->calculateBlockDuration($attempts);
            // Calculer le temps restant avant déblocage
            if (isset($_SESSION[self::SESSION_KEY][$email]['attempts']) && $blockDuration > 0) {
                $lastAttempt = max($_SESSION[self::SESSION_KEY][$email]['attempts']);
                $elapsedTime = time() - $lastAttempt;
                $remainingTime = max(0, ($blockDuration * 60) - $elapsedTime);
                return [
                    'blocked' => $remainingTime > 0,
                    'remaining_time' => $remainingTime,
                    'attempts' => $attempts,
                    'block_duration' => $blockDuration
                ];
            }
        }
        return [
            'blocked' => false,
            'remaining_time' => 0,
            'attempts' => $attempts,
            'block_duration' => 0
        ];
    }

    /**
     * Compte le nombre de tentatives par IP
     * @param string $ip L'adresse IP
     * @return int Nombre de tentatives
     */
    public function getFailedAttemptsCountByIP(string $ip): int
    {
        $this->initSession();
        $ipKey = 'ip_' . md5($ip);
        $this->cleanOldAttempts($ipKey);
        if (!isset($_SESSION[self::SESSION_KEY][$ipKey])) {
            return 0;
        }
        return $_SESSION[self::SESSION_KEY][$ipKey]['count'];
    }

    /**
     * Vérifie si une IP est bloquée
     * @param string $ip L'adresse IP
     * @return array ['blocked' => bool, 'remaining_time' => int, 'attempts' => int]
     */
    public function isIPBlocked(string $ip): array
    {
        $maxAttemptsIP = 10; // 10 tentatives max par IP
        $blockDurationIP = 30; // 30 minutes
        $this->initSession();
        $ipKey = 'ip_' . md5($ip);
        $this->cleanOldAttempts($ipKey);
        $attempts = $this->getFailedAttemptsCountByIP($ip);
        if ($attempts >= $maxAttemptsIP) {
            if (isset($_SESSION[self::SESSION_KEY][$ipKey]['attempts'])) {
                $lastAttempt = max($_SESSION[self::SESSION_KEY][$ipKey]['attempts']);
                $elapsedTime = time() - $lastAttempt;
                $remainingTime = max(0, ($blockDurationIP * 60) - $elapsedTime);
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
     * Supprime les tentatives d'un email après une connexion réussie
     * @param string $email L'email de l'utilisateur connecté
     */
    public function clearFailedAttempts(string $email): void
    {
        $this->initSession();
        if (isset($_SESSION[self::SESSION_KEY][$email])) {
            unset($_SESSION[self::SESSION_KEY][$email]);
        }
    }

    /**
     * Nettoie toutes les tentatives expirées (appelé périodiquement)
     */
    public function cleanupOldAttempts(): void
    {
        $this->initSession();
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return;
        }
        foreach (array_keys($_SESSION[self::SESSION_KEY]) as $key) {
            $this->cleanOldAttempts($key);
        }
    }
}
