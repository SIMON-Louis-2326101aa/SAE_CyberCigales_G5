<?php
/**
 * Classe SecurityHelper
 * Fournit des méthodes pour sécuriser l'application contre diverses attaques
 */
class SecurityHelper
{
    /**
     * Nettoie et valide une chaîne pour éviter les injections XSS
     * @param string $input La chaîne à nettoyer
     * @return string La chaîne nettoyée
     */
    public static function sanitizeString(string $input): string
    {
        // Supprime les espaces en début et fin
        $input = trim($input);
        
        // Convertit les caractères spéciaux en entités HTML
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $input;
    }

    /**
     * Valide et nettoie une adresse email
     * @param string $email L'email à valider
     * @return string|false L'email nettoyé ou false si invalide
     */
    public static function sanitizeEmail(string $email)
    {
        $email = trim($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        
        return false;
    }

    /**
     * Valide un nom (nom, prénom)
     * @param string $name Le nom à valider
     * @return bool True si valide, false sinon
     */
    public static function validateName(string $name): bool
    {
        // Autorise les lettres (avec accents), espaces, tirets et apostrophes
        // Minimum 2 caractères, maximum 50
        return preg_match("/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/u", $name);
    }

    /**
     * Génère un token CSRF sécurisé
     * @return string Le token généré
     */
    public static function generateCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie la validité d'un token CSRF
     * @param string $token Le token à vérifier
     * @return bool True si valide, false sinon
     */
    public static function verifyCSRFToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Nettoie les données d'un tableau $_POST ou $_GET
     * @param array $data Les données à nettoyer
     * @param array $exclude Clés à exclure du nettoyage (ex: mots de passe)
     * @return array Les données nettoyées
     */
    public static function sanitizeInput(array $data, array $exclude = []): array
    {
        $cleaned = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $exclude)) {
                // Ne pas nettoyer les champs exclus (comme les mots de passe)
                $cleaned[$key] = $value;
            } elseif (is_string($value)) {
                $cleaned[$key] = self::sanitizeString($value);
            } elseif (is_array($value)) {
                $cleaned[$key] = self::sanitizeInput($value, $exclude);
            } else {
                $cleaned[$key] = $value;
            }
        }
        
        return $cleaned;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     * @return bool True si connecté, false sinon
     */
    public static function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté
     * @param string $redirectUrl URL de redirection (optionnel)
     */
    public static function requireLogin(string $redirectUrl = 'index.php?controller=redirection&action=openFormConnection'): void
    {
        if (!self::isLoggedIn()) {
            header("Location: $redirectUrl");
            exit();
        }
    }

    /**
     * Régénère l'ID de session pour prévenir la fixation de session
     */
    public static function regenerateSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_regenerate_id(true);
    }

    /**
     * Protège contre les attaques par force brute en ajoutant un délai
     * @param int $seconds Nombre de secondes à attendre
     */
    public static function addDelay(int $seconds = 2): void
    {
        sleep($seconds);
    }

    /**
     * Valide la force d'un mot de passe
     * @param string $password Le mot de passe à valider
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePassword(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule.";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial.";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Détecte les tentatives d'injection SQL dans une chaîne
     * @param string $input La chaîne à vérifier
     * @return bool True si une tentative d'injection est détectée
     */
    public static function detectSQLInjection(string $input): bool
    {
        $sqlKeywords = [
            'UNION', 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'CREATE',
            'ALTER', 'EXEC', 'EXECUTE', 'SCRIPT', '--', '/*', '*/', 'xp_', 'sp_'
        ];
        
        $inputUpper = strtoupper($input);
        
        foreach ($sqlKeywords as $keyword) {
            if (strpos($inputUpper, $keyword) !== false) {
                // Log la tentative d'injection
                self::logSecurityEvent('SQL Injection Attempt', $input);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Détecte les tentatives de XSS dans une chaîne
     * @param string $input La chaîne à vérifier
     * @return bool True si une tentative de XSS est détectée
     */
    public static function detectXSS(string $input): bool
    {
        $xssPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i', // onclick, onload, etc.
            '/<iframe/i',
            '/<object/i',
            '/<embed/i'
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                // Log la tentative de XSS
                self::logSecurityEvent('XSS Attempt', $input);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Enregistre un événement de sécurité
     * @param string $type Type d'événement
     * @param string $details Détails de l'événement
     */
    private static function logSecurityEvent(string $type, string $details): void
    {
        $logFile = __DIR__ . '/../logs/security.log';
        $logDir = dirname($logFile);
        
        // Créer le dossier logs s'il n'existe pas
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $logMessage = sprintf(
            "[%s] %s - IP: %s - User-Agent: %s - Details: %s\n",
            $timestamp,
            $type,
            $ip,
            $userAgent,
            $details
        );
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Configure les en-têtes HTTP de sécurité
     */
    public static function setSecurityHeaders(): void
    {
        // Protection contre le clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // Protection XSS intégrée au navigateur
        header('X-XSS-Protection: 1; mode=block');
        
        // Empêche le navigateur de deviner le type MIME
        header('X-Content-Type-Options: nosniff');
        
        // Politique de sécurité du contenu (CSP)
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
        
        // Force HTTPS (si disponible)
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Protection contre les référents
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
