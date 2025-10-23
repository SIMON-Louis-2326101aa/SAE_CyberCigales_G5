<?php
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

class userController
{
    private userModel $userModel;

    public function __construct()
    {
        $this->userModel = new userModel();
        if (function_exists('log_console')) {
            log_console('userController initialisé', 'ok'); // ✅
        }
    }

    /**
     * Inscription utilisateur
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['register'])) {
            header("Location: index.php?controller=redirection&action=openFormRegister");
            exit;
        }

        $nom      = trim($_POST['nom'] ?? '');
        $prenom   = trim($_POST['prenom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['pwd'] ?? '';
        $confirm  = $_POST['confirm_pwd'] ?? '';

        // Vérif basique mots de passe
        if ($password !== $confirm) {
            $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";
            if (function_exists('log_console')) log_console('Register: mots de passe non identiques', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openFormRegister");
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['flash_error'] = "Votre mot de passe n'est pas assez long : minimum 8 caractères.";
            if (function_exists('log_console')) log_console('Register: mot de passe < 8 caractères', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openFormRegister");
            exit;
        }

        // Règles de complexité
        $verif_majuscule = '/[A-Z]/';
        $verif_minuscule = '/[a-z]/';
        $verif_chiffre   = '/[0-9]/';
        $verif_special   = '/[^a-zA-Z0-9]/';

        if (
            !preg_match($verif_majuscule, $password) ||
            !preg_match($verif_minuscule, $password) ||
            !preg_match($verif_chiffre, $password)   ||
            !preg_match($verif_special, $password)
        ) {
            $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            if (function_exists('log_console')) log_console('Register: complexité mot de passe insuffisante', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openFormRegister");
            exit;
        }

        // E-mail déjà utilisé ?
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['flash_error'] = "Impossible de créer le compte. Veuillez vérifier les informations saisies.";
            if (function_exists('log_console')) log_console("Register: email déjà utilisé ($email)", 'info'); // ℹ️
            header("Location: index.php?controller=redirection&action=openFormRegister");
            exit;
        }

        // Création
        $success = $this->userModel->register($nom, $prenom, $email, $password);

        if (!$success) {
            $_SESSION['flash_error'] = "Erreur lors de l'inscription.";
            if (function_exists('log_console')) log_console("Register: échec insertion DB ($email)", 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openFormRegister");
            exit;
        }

        // Connexion automatique après inscription
        $utilisateur = $this->userModel->authenticate($email, $password);
        if ($utilisateur) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start([
                    'use_strict_mode' => true,
                    'cookie_httponly' => true,
                    'cookie_secure'   => true,
                    'cookie_samesite' => 'None'
                ]);
            }

            $_SESSION['utilisateur'] = $utilisateur;
            $_SESSION['user_id']     = $utilisateur['id'] ?? null;
            $_SESSION['nom']         = $utilisateur['nom'] ?? null;
            $_SESSION['prenom']      = $utilisateur['prenom'] ?? null;
            $_SESSION['email']       = $utilisateur['email'] ?? null;

            if (function_exists('log_console')) log_console("Register: inscription + login OK ($email)", 'ok'); // ✅
            header("Location: index.php?controller=redirection&action=openHomepage");
            exit;
        }

        // Fallback : inscrit mais pas loggé
        $_SESSION['flash_error'] = "Inscription réussie, mais problème de connexion automatique.";
        if (function_exists('log_console')) log_console("Register: login auto échoué ($email)", 'error'); // ❌
        header("Location: index.php?controller=redirection&action=openFormConnection");
        exit;
    }

    /**
     * Connexion
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
            header("Location: index.php?controller=redirection&action=openFormConnection");
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['pwd'] ?? '';

        $utilisateur = $this->userModel->authenticate($email, $password);

        if ($utilisateur) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start([
                    'use_strict_mode' => true,
                    'cookie_httponly' => true,
                    'cookie_secure'   => true,
                    'cookie_samesite' => 'None'
                ]);
            }

            $_SESSION['utilisateur'] = $utilisateur;
            $_SESSION['user_id']     = $utilisateur['id'] ?? null;
            $_SESSION['nom']         = $utilisateur['nom'] ?? null;
            $_SESSION['prenom']      = $utilisateur['prenom'] ?? null;
            $_SESSION['email']       = $utilisateur['email'] ?? null;

            if (function_exists('log_console')) log_console("Login: succès ($email)", 'ok'); // ✅
            header("Location: index.php?controller=redirection&action=openHomepage");
            exit;
        }

        // Échec login
        $_SESSION['flash_error'] = "Email ou mot de passe incorrect.";
        if (function_exists('log_console')) log_console("Login: échec ($email)", 'error'); // ❌
        header("Location: index.php?controller=redirection&action=openFormConnection");
        exit;
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();

        if (function_exists('log_console')) log_console('Logout: session détruite', 'info'); // ℹ️
        header("Location: index.php?controller=redirection&action=openHomepage");
        exit;
    }

    /**
     * Mot de passe oublié : envoi lien
     */
    public function forgot()
    {
        if (!isset($_POST['forgotPwd'])) {
            header("Location: index.php?controller=redirection&action=openForgotPwd");
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (!$this->userModel->emailExists($email)) {
            $_SESSION['flash_error'] = "L'email n'existe pas ! Veuillez retourner en arrière pour vous inscrire.";
            if (function_exists('log_console')) log_console("Forgot: email inconnu ($email)", 'info'); // ℹ️
            header("Location: index.php?controller=redirection&action=openForgotPwd");
            exit;
        }

        // Mémorise l'email pour le changement de mot de passe
        $_SESSION['email'] = $email;

        // Envoi du mail (simple)
        $to      = $email;
        $subject = 'Reinitialisation du mot de passe';
        $message = 'Bonjour !
Pour reinitialiser votre mot de passe cliquez sur le lien suivant: 
https://escapethecode.alwaysdata.net/index.php?controller=redirection&action=openChangePwd';

        $sent = @mail($to, $subject, $message);

        if ($sent) {
            $_SESSION['flash_success'] = "Un lien de réinitialisation vous a été envoyé.";
            if (function_exists('log_console')) log_console("Forgot: mail envoyé ($email)", 'ok'); // ✅
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi du mail. Veuillez réessayer.";
            if (function_exists('log_console')) log_console("Forgot: échec envoi mail ($email)", 'error'); // ❌
        }

        header("Location: index.php?controller=redirection&action=openForgotPwd");
        exit;
    }

    /**
     * Changement de mot de passe
     */
    public function changePwd()
    {
        if (!isset($_POST['changePwd'])) {
            header("Location: index.php?controller=redirection&action=openChangePwd");
            exit;
        }

        $newPassword      = $_POST['new_password'] ?? '';
        $confirmPassword  = $_POST['confirm_password'] ?? '';
        $email            = $_SESSION['email'] ?? '';

        if (strlen($newPassword) < 8) {
            $_SESSION['flash_error'] = "Votre mot de passe n'est pas assez long : minimum 8 caractères.";
            if (function_exists('log_console')) log_console('ChangePwd: mot de passe < 8', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openChangePwd");
            exit;
        }

        $verif_majuscule = '/[A-Z]/';
        $verif_minuscule = '/[a-z]/';
        $verif_chiffre   = '/[0-9]/';
        $verif_special   = '/[^a-zA-Z0-9]/';

        if (
            !preg_match($verif_majuscule, $newPassword) ||
            !preg_match($verif_minuscule, $newPassword) ||
            !preg_match($verif_chiffre,   $newPassword) ||
            !preg_match($verif_special,   $newPassword)
        ) {
            $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            if (function_exists('log_console')) log_console('ChangePwd: complexité insuffisante', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openChangePwd");
            exit;
        }

        if (empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['flash_error'] = "Veuillez remplir les deux champs de mot de passe.";
            if (function_exists('log_console')) log_console('ChangePwd: champs vides', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openChangePwd");
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";
            if (function_exists('log_console')) log_console('ChangePwd: mots de passe différents', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openChangePwd");
            exit;
        }

        $ok = $this->userModel->changePwd($newPassword, $email);
        if ($ok) {
            $_SESSION['flash_success'] = "Votre mot de passe a été modifié avec succès.";
            if (function_exists('log_console')) log_console("ChangePwd: succès ($email)", 'ok'); // ✅
        } else {
            $_SESSION['flash_error'] = "Erreur lors de la modification du mot de passe.";
            if (function_exists('log_console')) log_console("ChangePwd: échec ($email)", 'error'); // ❌
        }

        header("Location: index.php?controller=redirection&action=openChangePwd");
        exit;
    }

    /**
     * Page compte (suppression)
     */
    public function account()
    {
        if (isset($_POST['delete'])) {
            $email = $_SESSION['email'] ?? null;

            if ($email && $this->userModel->delete($email)) {
                if (function_exists('log_console')) log_console("Account: suppression utilisateur ($email)", 'file'); // 📄
                session_destroy();
                header("Location: index.php?controller=redirection&action=openHomepage");
                exit;
            }

            $_SESSION['flash_error'] = "Une erreur est survenue lors de la suppression de votre compte.";
            if (function_exists('log_console')) log_console("Account: échec suppression ($email)", 'error'); // ❌
        }

        header("Location: index.php?controller=redirection&action=openAccount");
        exit;
    }
}
