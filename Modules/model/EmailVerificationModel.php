<?php

//require_once __DIR__ . '/Database.php';

namespace SAE_CyberCigales_G5\Modules\model;

use PDO;

/**
 * Modèle de vérification d'email
 * 
 * Gère la génération, le stockage et la vérification des codes de vérification d'email,
 * ainsi que les inscriptions en attente de vérification.
 * 
 * @package SAE_CyberCigales_G5\Modules\model
 * @author Équipe CyberCigales
 */
class EmailVerificationModel extends Database
{
    /**
     * Génère un code de vérification à 6 chiffres
     * 
     * Méthode statique pour générer un code aléatoire de 6 chiffres.
     * Utilise random_int() pour la sécurité cryptographique.
     * 
     * @return string Code à 6 chiffres (avec zéros à gauche si nécessaire)
     */
    public static function generateCode(): string
    {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Valide et limite le TTL entre 1 et 60 minutes
     * 
     * @param int $ttlMinutes TTL en minutes
     * @return int TTL limité entre 1 et 60
     */
    public static function validateTTL(int $ttlMinutes): int
    {
        return max(1, min(60, $ttlMinutes));
    }

    /**
     * Génère et stocke un code de vérification à 6 chiffres
     * 
     * Le code expire après le délai spécifié (par défaut 10 minutes).
     * Utilise l'horloge de la base de données pour éviter les décalages.
     * 
     * @param string $email Email pour lequel générer le code
     * @param int $ttlMinutes Durée de validité du code en minutes (min: 1, max: 60)
     * @return string Le code généré (6 chiffres)
     */
    public function generateAndStoreCode(string $email, int $ttlMinutes = 10): string
    {
        $code = self::generateCode();
        $ttl = self::validateTTL($ttlMinutes);

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

    /**
     * Vérifie le statut d'un code de vérification
     * 
     * Détermine si le code est valide, expiré ou incorrect.
     * 
     * @param string $email Email associé au code
     * @param string $code Code à vérifier
     * @return array Tableau associatif avec 'valid' (bool) et 'reason' (string: 'valid', 'expired', 'incorrect')
     */
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

    /**
     * Stocke une inscription en attente de vérification
     * 
     * Sauvegarde les données d'inscription dans la table pending_registrations
     * en attendant la vérification de l'email. Supprime toute inscription
     * en attente existante pour cet email avant d'en créer une nouvelle.
     * 
     * @param string $nom Nom de l'utilisateur
     * @param string $prenom Prénom de l'utilisateur
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe hashé
     * @return bool True en cas de succès, false sinon
     */
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
}
