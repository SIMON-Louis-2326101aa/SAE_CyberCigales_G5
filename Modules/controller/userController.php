<?php
/**
 * Contrôleur de gestion des utilisateurs
 * 
 * Ce contrôleur gère toutes les actions liées aux utilisateurs :
 * - Inscription (register)
 * - Connexion (login)
 * - Déconnexion (logout)
 * - Mot de passe oublié (forgot)
 * - Changement de mot de passe (changePwd)
 * - Gestion du compte (account)
 * 
 * @author SAE CyberCigales G5
 * @version 1.0
 */

require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

class userController
{
    /**
     * @var userModel Instance du modèle utilisateur
     */
    private $userModel;
    
    /**
     * Constructeur - initialise le modèle utilisateur
     */
    public function __construct() {
        $this->userModel = new userModel();
    }
    
    /**
     * Gère l'inscription d'un nouvel utilisateur
     * 
     * Valide les données du formulaire :
     * - Vérification de la correspondance des mots de passe
     * - Validation de la complexité du mot de passe (8 car min, majuscule, minuscule, chiffre, caractère spécial)
     * - Vérification que l'email n'existe pas déjà
     * - Connexion automatique après inscription réussie
     * 
     * @return void Redirige vers la page appropriée
     */
    public function register() {
        if (isset($_POST['register'])) {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['pwd'] ?? '';
            $confirm = $_POST['confirm_pwd'] ?? '';

            // Vérification 1 : Les deux mots de passe doivent être identiques
            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                header("Location: index.php?controller=redirection&action=openFormRegister");
                echo $error;
                return;
            }

            // Vérification 2 : Longueur minimale du mot de passe (8 caractères)
            if(strlen($password) < 8) {
                $error = "Votre mot de passe n'est pas assez long : minimun 8 caractères";
                viewHandler::show("../view/formRegisterView");
                echo $error;
                return;
            }

            // Vérification 3 : Complexité du mot de passe (sécurité renforcée)
            $verif_majuscule = '/[A-Z]/';           // Au moins une majuscule
            $verif_minuscule = '/[a-z]/';           // Au moins une minuscule
            $verif_chiffre = '/[0-9]/';             // Au moins un chiffre
            $verif_special = '/[^a-zA-Z0-9]/';      // Au moins un caractère spécial

            if (!preg_match($verif_majuscule, $password) ||
                !preg_match($verif_minuscule, $password) ||
                !preg_match($verif_chiffre, $password) ||
                !preg_match($verif_special, $password))
            {
                $error = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                viewHandler::show("../view/formRegisterView");
                echo $error;
                return;
            }

            // Vérification 4 : L'email ne doit pas déjà être utilisé
            if ($this->userModel->findByEmail($email)) {
                $error = "Impossible de créer le compte. Veuillez vérifier les informations saisies.";
                header("Location: index.php?controller=redirection&action=openFormRegister");
                echo $error;
                return;
            }

            // Création du compte utilisateur dans la base de données
            $success = $this->userModel->register($nom, $prenom, $email, $password);

            if ($success) {
                // Connexion automatique après inscription réussie
                $connexionModel = new userModel();
                $utilisateur = $connexionModel->authenticate($email, $password);

                if ($utilisateur) {
                    // Démarrage de la session avec paramètres de sécurité
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start([
                            'use_strict_mode' => true,     // Empêche l'utilisation de sessions non initialisées
                            'cookie_httponly' => true,     // Protection contre XSS
                            'cookie_secure' => true,       // Transmission sécurisée (HTTPS)
                            'cookie_samesite' => 'None'    // Protection CSRF
                        ]);
                    }

                    // Stockage des informations utilisateur en session
                    $_SESSION['utilisateur'] = $utilisateur;
                    $_SESSION['user_id'] = $utilisateur['id'];
                    $_SESSION['nom'] = $utilisateur['nom'];
                    $_SESSION['prenom'] = $utilisateur['prenom'];
                    $_SESSION['email'] = $utilisateur['email'];

                    // Redirection vers la page d'accueil
                    header("Location: index.php?controller=redirection&action=openHomepage");
                    exit();
                } else {
                    // Fallback : inscription réussie mais échec de connexion automatique
                    $error = "Inscription réussie, mais problème de connexion automatique.";
                    header("Location: index.php?controller=redirection&action=openFormConnection");
                    echo $error;
                    return;
                }
            } else {
                // Erreur lors de la création du compte
                $error = "Erreur lors de l'inscription.";
                header("Location: index.php?controller=redirection&action=openFormRegister");
                echo $error;
                return;
            }
        }
        // Redirection par défaut si aucune soumission de formulaire
        header("Location: index.php?controller=redirection&action=openFormRegister");
    }

    /**
     * Gère la connexion d'un utilisateur existant
     * 
     * Vérifie les identifiants (email + mot de passe) et crée une session
     * si l'authentification réussit.
     * 
     * @return void Redirige vers la page d'accueil si succès, vers le formulaire sinon
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['pwd'] ?? '';

            // Vérification des identifiants via le modèle
            $utilisateur = $this->userModel->authenticate($email, $password);

            if ($utilisateur) {
                // Authentification réussie - création de la session
                if (session_status() == PHP_SESSION_NONE) {
                    session_start([
                        'use_strict_mode' => true,     // Empêche l'utilisation de sessions non initialisées
                        'cookie_httponly' => true,     // Protection contre XSS
                        'cookie_secure' => true,       // Transmission sécurisée (HTTPS)
                        'cookie_samesite' => 'None'    // Protection CSRF
                    ]);
                }

                // Stockage des informations utilisateur en session
                $_SESSION['utilisateur'] = $utilisateur;
                $_SESSION['user_id'] = $utilisateur['id'];
                $_SESSION['nom'] = $utilisateur['nom'];
                $_SESSION['prenom'] = $utilisateur['prenom'];
                $_SESSION['email'] = $utilisateur['email'];

                // Redirection vers la page d'accueil
                header("Location: index.php?controller=redirection&action=openHomepage");
                exit();
            } else {
                // Échec de l'authentification
                $error = "Email ou mot de passe incorrect.";
                header("Location: index.php?controller=redirection&action=openFormConnection");
                echo $error;
                return;
            }
        }
        // Redirection par défaut si aucune soumission
        header("Location: index.php?controller=redirection&action=openFormConnection");
    }

    /**
     * Gère la déconnexion de l'utilisateur
     * 
     * Détruit la session active et supprime tous les cookies associés.
     * 
     * @return void Redirige vers la page d'accueil
     */
    public function logout() {
        // Démarrage de la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Effacement de toutes les variables de session
        $_SESSION = array();

        // Suppression du cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        // Destruction complète de la session
        session_destroy();

        // Redirection vers la page d'accueil
        header("Location: index.php?controller=redirection&action=openHomepage");
        exit();
    }
    
    /**
     * Gère la demande de réinitialisation du mot de passe
     * 
     * Vérifie que l'email existe et envoie un lien de réinitialisation par email.
     * 
     * @return void Redirige vers le formulaire avec un message de succès ou d'erreur
     */
    public function forgot(){
        if (isset($_POST['forgotPwd'])) {
            $email = $_POST['email'] ?? '';
            
            // Vérifier que l'email existe dans la base de données
            if (!$this->userModel->emailExists($email)) {
                $data['error'] = "L'email n'existe pas ! Veuillez retourner en arriere pour vous inscrire.";
                echo $data['error'];
            } else {
                // Stockage de l'email en session pour la réinitialisation
                $_SESSION['email'] = $email;

                // Préparation de l'email de réinitialisation
                $to = $email;
                $subject = 'Reinitialisation du mot de passe';
                $message = 'Bonjour ! 
                Pour reinitialiser votre mot de passe cliquer sur le lien suivant: 
                https://escapethecode.alwaysdata.net/index.php?controller=redirection&action=openChangePwd';

                // Envoi de l'email
                if (mail($to, $subject, $message)) {
                    $data['success'] = "Un lien de réinitialisation vous a été envoyé.";
                    echo $data['success'];
                } else {
                    $data['error'] = "Erreur lors de l'envoie du mail. Veuillez réessayer.";
                    echo $data['error'];
                }
            }
        }
        // Redirection vers le formulaire mot de passe oublié
        header("Location: index.php?controller=redirection&action=openForgotPwd");
    }
    
    /**
     * Gère le changement de mot de passe
     * 
     * Valide le nouveau mot de passe (longueur, complexité) et met à jour en base de données.
     * 
     * @return void Redirige vers le formulaire avec un message de succès ou d'erreur
     */
    public function changePwd(){
        if (isset($_POST['changePwd'])) {
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $email = $_SESSION['email'];

            // Vérification 1 : Longueur minimale du mot de passe (8 caractères)
            if(strlen($newPassword) < 8) {
                $error = "Votre mot de passe n'est pas assez long : minimun 8 caractères";
                viewHandler::show("../view/changePwdView");
                echo $error;
                return;
            }

            // Vérification 2 : Complexité du mot de passe (sécurité renforcée)
            $verif_majuscule = '/[A-Z]/';           // Au moins une majuscule
            $verif_minuscule = '/[a-z]/';           // Au moins une minuscule
            $verif_chiffre = '/[0-9]/';             // Au moins un chiffre
            $verif_special = '/[^a-zA-Z0-9]/';      // Au moins un caractère spécial

            if (!preg_match($verif_majuscule, $newPassword) ||
                !preg_match($verif_minuscule, $newPassword) ||
                !preg_match($verif_chiffre, $newPassword) ||
                !preg_match($verif_special, $newPassword))
            {
                $error = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                viewHandler::show("../view/changePwdView");
                echo $error;
                return;
            }

            // Vérification 3 : Les champs ne doivent pas être vides
            if (empty($newPassword) || empty($confirmPassword)) {
                $data['error'] = "Veuillez remplir les deux champs de mot de passe.";
                echo $data['error'];
            } 
            // Vérification 4 : Les deux mots de passe doivent correspondre
            elseif ($newPassword !== $confirmPassword) {
                $data['error'] = "Les mots de passe ne correspondent pas.";
                echo $data['error'];
            } 
            // Mise à jour du mot de passe dans la base de données
            else {
                if ($this->userModel->changePwd($newPassword, $email)) {
                    $data['success'] = "Votre mot de passe a été modifié avec succès.";
                    echo $data['success'];
                } else {
                    $data['error'] = "Erreur lors de la modification du mot de passe.";
                    echo $data['error'];
                }
            }
        }
        // Redirection vers le formulaire de changement de mot de passe
        header("Location: /index.php?controller=redirection&action=openChangePwd");
    }
    
    /**
     * Gère la page de compte utilisateur et la suppression de compte
     * 
     * Permet à l'utilisateur de supprimer son compte.
     * La suppression détruit également la session active.
     * 
     * @return void Redirige vers la page de compte ou la page d'accueil après suppression
     */
    public function account(){
        // Gestion de la suppression de compte
        if (isset($_POST['delete'])) {
            $email = $_SESSION['email'];
            
            // Suppression du compte dans la base de données
            if ($this->userModel->delete($email)) {
                // Destruction de la session après suppression
                session_destroy();
                header("Location: /index.php?controller=redirection&action=openHomepage");
                exit();
            } else {
                $data['error'] = "Une erreur est survenue lors de la suppression de votre compte.";
                echo $data['error'];
            }
        }

        // Redirection par défaut vers la page de compte
        header("Location: /index.php?controller=redirection&action=openAccount");
    }
}
