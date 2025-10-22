<?php
/**
 * Modèle de gestion des utilisateurs
 * 
 * Ce modèle gère toutes les opérations liées aux utilisateurs dans la base de données :
 * - Inscription (register)
 * - Authentification (authenticate)
 * - Recherche par email (findByEmail)
 * - Vérification d'existence d'email (emailExists)
 * - Changement de mot de passe (changePwd)
 * - Suppression de compte (delete)
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../../includes/connectionDB.php';

class userModel extends database
{
    /**
     * @var connectionDB Instance de connexion à la base de données
     */
    private connectionDB $db;

    /**
     * Constructeur - initialise la connexion à la base de données
     */
    public function __construct()
    {
        $this->db = connectionDB::getInstance();
    }
    
    /**
     * Inscrit un nouvel utilisateur dans la base de données
     * 
     * Hache le mot de passe avant de le stocker (sécurité)
     * 
     * @param string $nom Nom de famille
     * @param string $prenom Prénom
     * @param string $email Adresse email (doit être unique)
     * @param string $password Mot de passe en clair (sera haché)
     * @return bool True si l'inscription réussit, False sinon
     */
    public function register(string $nom, string $prenom, string $email, string $password): bool
    {
        $sql = "INSERT INTO users (nom, prenom, email, password) VALUES (:nom, :prenom, :email, :password)";
        $stmt = $this->getBdd()->prepare($sql);
        
        // Hachage sécurisé du mot de passe avec BCRYPT
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $hash
        ]);
    }
    
    /**
     * Vérifie si un email existe déjà dans la base de données
     * 
     * @param string $email Email à vérifier
     * @return bool True si l'email existe, False sinon
     */
    public function findByEmail($email): bool
    {
        $bdd = $this->getBdd();
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Retourne true si au moins un utilisateur avec cet email existe
        return $result['count'] > 0;
    }
    
    /**
     * Authentifie un utilisateur avec son email et mot de passe
     * 
     * Vérifie le mot de passe haché de manière sécurisée.
     * 
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe en clair
     * @return array|null Données de l'utilisateur si authentification réussie, null sinon
     */
    public function authenticate($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification sécurisée du mot de passe haché
        if ($user && password_verify($password, $user['password'])) {
            return $user;  // Retourne toutes les données de l'utilisateur
        }
        
        return null;  // Authentification échouée
    }
    
    /**
     * Vérifie si un email existe dans la base de données
     * 
     * Méthode alternative à findByEmail()
     * 
     * @param string $email Email à vérifier
     * @return bool True si l'email existe, False sinon
     */
    public function emailExists(string $email): bool
    {
        $bdd = $this->getBdd();
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        // Retourne true si au moins un utilisateur trouvé
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Change le mot de passe d'un utilisateur
     * 
     * Hache le nouveau mot de passe avant de le stocker.
     * 
     * @param string $newPassword Nouveau mot de passe en clair
     * @param string $email Email de l'utilisateur
     * @return bool True si la modification réussit, False sinon
     */
    public function changePwd(string $newPassword, string $email): bool
    {
        // Hachage sécurisé du nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $bdd = $this->getBdd();
        $sql = "UPDATE users SET  password = :newPassword WHERE email = :email";
        $stmt = $bdd->prepare($sql);
        
        return $stmt->execute([
            'newPassword' => $hashedPassword,
            'email' => $email
        ]);
    }
    
    /**
     * Supprime un compte utilisateur de la base de données
     * 
     * @param string $email Email de l'utilisateur à supprimer
     * @return bool True si la suppression réussit, False sinon
     */
    public function delete(string $email): bool
    {
        $sql = "DELETE FROM users WHERE email = :email";
        $stmt = $this->getBdd()->prepare($sql);
        return $stmt->execute([
            'email' => $email,
        ]);
    }
}