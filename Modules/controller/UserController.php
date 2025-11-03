<?php

namespace SAE_CyberCigales_G5\Modules\controller;

//require_once __DIR__ . '/../model/UserModel.php';
//require_once __DIR__ . '/../model/EmailVerificationModel.php';
//require_once __DIR__ . '/../model/PasswordResetModel.php'; // nécessaire pour forgot/changePwd
//require_once __DIR__ . '/../../includes/ViewHandler.php';
//require_once __DIR__ . '/../../includes/Mailer.php';

use SAE_CyberCigales_G5\includes\Constant;
use SAE_CyberCigales_G5\includes\Mailer;
use SAE_CyberCigales_G5\includes\ViewHandler;
use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel;
use SAE_CyberCigales_G5\Modules\model\PasswordResetModel;
use SAE_CyberCigales_G5\Modules\model\UserModel;

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        if (function_exists('log_console')) {
            log_console('userController initialisé', 'ok');
        }
    }

    /**
     * Inscription utilisateur avec vérification e-mail (code à 10 min)
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['register'])) {
            $_SESSION['flash_error'] = "Accès invalide au formulaire d'inscription.";
            //if (function_exists('log_console')) log_console('Register: accès direct ou mauvaise méthode', 'error');
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        $nom      = trim($_POST['nom']     ?? '');
        $prenom   = trim($_POST['prenom']  ?? '');
        $email    = trim($_POST['email']   ?? '');
        $password = $_POST['pwd']          ?? '';
        $confirm  = $_POST['confirm_pwd']  ?? '';

        unset($_SESSION['old']); // nettoie une éventuelle valeur polluée

        $sanitize = static function (string $s): string {
            $s = strip_tags($s);               // vire toute balise
            $s = preg_replace('/\s+/', ' ', $s);
            return mb_substr(trim($s), 0, 120);
        };

        // stocker les valeurs
        $_SESSION['old'] = [
            'nom'    => $sanitize($nom),
            'prenom' => $sanitize($prenom),
            'email'  => $sanitize($email),
        ];

        // Vérification mots de passe
        if ($password !== $confirm) {
            $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";
            //if (function_exists('log_console')) log_console('Register: mots de passe différents', 'error');
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }
        if (strlen($password) < 8) {
            $_SESSION['flash_error'] = "Votre mot de passe n'est pas assez long : minimum 8 caractères.";
            //if (function_exists('log_console')) log_console('Register: mot de passe < 8 caractères', 'error');
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }
        // Complexité
        if (
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^a-zA-Z0-9]/', $password)
        ) {
            $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, 
            une minuscule, un chiffre et un caractère spécial.";
            //if (function_exists('log_console')) log_console('Register: complexité insuffisante', 'error');
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        // E-mail déjà utilisé ?
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['flash_error'] = "Impossible de créer le compte. Veuillez vérifier les informations saisies.";
            //if (function_exists('log_console')) log_console("Register: email déjà utilisé ($email)", 'info');
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        // Vérifier si inscription déjà en attente
        $emailModel  = new EmailVerificationModel();
        $emailStatus = $this->userModel->getEmailStatus($email); // suppose un array ['pending' => bool]

        if (!empty($emailStatus['pending'])) {
            // Renvoi d’un nouveau code
            $code = $emailModel->generateAndStoreCode($email);
            $subject = 'Vérification de votre adresse email';
            $message = '
<div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="center">
    <table width="600" style="background:#ffffff; padding:20px; border-radius:8px;
     box-shadow:0 4px 8px rgba(0,0,0,0.1);">
      <tr><td style="text-align:center;">
        <h2 style="color:#333333;">Vérification de votre adresse email</h2>
        <p style="font-size:16px; color:#555555;">Merci de vous être inscrit !</p>
        <p style="font-size:16px; color:#555555;">Votre code de vérification est :</p>
        <p style="font-size:24px; font-weight:bold; color:#007bff; background:#e9f7ff; padding:10px; 
        border-radius:4px; display:inline-block;">' . htmlspecialchars($code) . '</p>
        <p style="font-size:14px; color:#888888;">Ce code expire dans 10 minutes.</p>
      </td></tr>
    </table>
  </td></tr></table>
</div>';
            $sent = Mailer::send($email, $subject, $message);

            if ($sent) {
                $_SESSION['flash_success'] = "Un nouveau code vous a été envoyé.";
                //if (function_exists('log_console')) log_console("Register: renvoi code OK ($email)", 'ok');
            } else {
                // En local on peut exposer le code dans les logs console
                if (class_exists('Constant') && method_exists('Constant', 'isDev') && Constant::isDev()) {
                    $_SESSION['flash_success'] = "Envoi d'email indisponible en local. 
                    Utilisez le code affiché en console.";
                    //if (function_exists('log_console')) log_console
                    //("Register: email non envoyé (local) — code: {$code}, 'info');
                } else {
                    $_SESSION['flash_error'] = "L'envoi de l'email a échoué. Veuillez réessayer plus tard.";
                    //if (function_exists('log_console')) log_console("Register: échec envoi mail ($email)", 'error');
                }
            }

            // Succès logique → on peut vider le old
            unset($_SESSION['old']);

            header("Location: index.php?controller=Redirection&action=openEmailVerification&email="
                . urlencode($email));
            exit;
        }

        // Créer l’inscription en attente
        $stored = $emailModel->
        storePendingRegistration($nom, $prenom, $email, password_hash($password, PASSWORD_BCRYPT));
        if (!$stored) {
            $_SESSION['flash_error'] = "Erreur lors de l'inscription.";
            //if (function_exists('log_console')) log_console("Register: échec insertion DB ($email)", 'error');
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        // Générer et envoyer le code
        $code = $emailModel->generateAndStoreCode($email);
        $subject = 'Vérification de votre adresse email';
        $message = '
<div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="center">
    <table width="600" style="background:#ffffff; padding:20px; border-radius:8px; 
    box-shadow:0 4px 8px rgba(0,0,0,0.1);">
      <tr><td style="text-align:center;">
        <h2 style="color:#333333;">Vérification de votre adresse email</h2>
        <p style="font-size:16px; color:#555555;">Merci de vous être inscrit !</p>
        <p style="font-size:16px; color:#555555;">Votre code de vérification est :</p>
        <p style="font-size:24px; font-weight:bold; color:#007bff; background:#e9f7ff; 
        padding:10px; border-radius:4px; display:inline-block;">' . htmlspecialchars($code) . '</p>
        <p style="font-size:14px; color:#888888;">Ce code expire dans 10 minutes.</p>
      </td></tr>
    </table>
  </td></tr></table>
</div>';
        $sent = Mailer::send($email, $subject, $message);

        if ($sent) {
            $_SESSION['flash_success'] = "Un code vous a été envoyé. Vérifiez votre boîte mail.";
            //if (function_exists('log_console')) log_console("Register: code envoyé ($email)", 'ok');
        } else {
            if (class_exists('Constant') && method_exists('Constant', 'isDev') && Constant::isDev()) {
                $_SESSION['flash_success'] = "Envoi d'email indisponible en local. Utilisez le code affiché en console";
                //if (function_exists('log_console')) log_console("Register: email non envoyé (local)
                // — code: {$code}", 'info');
            } else {
                $_SESSION['flash_error'] = "L'envoi de l'email a échoué. Veuillez réessayer plus tard.";
                //if (function_exists('log_console')) log_console("Register: échec envoi mail ($email)", 'error');
            }
        }

        // Succès logique → on peut vider le old
        unset($_SESSION['old']);

        header("Location: index.php?controller=Redirection&action=openEmailVerification&email=" . urlencode($email));
        exit;
    }

    /**
     * Connexion
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'  || !isset($_POST['login'])) {
            $_SESSION['flash_error'] = "Accès invalide au formulaire de connexion.";
            header("Location: index.php?controller=Redirection&action=openFormConnection");
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['pwd'] ?? '';

        $utilisateur = $this->userModel->authenticate($email, $password);

        if ($utilisateur) {
            // La session est supposée démarrée dans index.php
            $_SESSION['utilisateur'] = $utilisateur;
            $_SESSION['user_id']     = $utilisateur['id']     ?? null;
            $_SESSION['nom']         = $utilisateur['nom']    ?? null;
            $_SESSION['prenom']      = $utilisateur['prenom'] ?? null;
            $_SESSION['email']       = $utilisateur['email']  ?? null;

            $_SESSION['flash_success'] = "Connexion réussie.";
            //if (function_exists('log_console')) log_console("Login: succès ($email)", 'ok');

            // Succès → on peut nettoyer le old
            unset($_SESSION['old']);

            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        // Échec : garder l'email saisi
        $_SESSION['old'] = ['email' => $email];

        $_SESSION['flash_error'] = "Email ou mot de passe incorrect.";
        //if (function_exists('log_console')) log_console("Login: échec ($email)", 'error');
        header("Location: index.php?controller=Redirection&action=openFormConnection");
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

        // Nettoyer uniquement les infos utilisateur
        unset(
            $_SESSION['utilisateur'],
            $_SESSION['user_id'],
            $_SESSION['nom'],
            $_SESSION['prenom'],
            $_SESSION['email']
        );

        // Sécurité: nouvelle session sans invalider le flash
        session_regenerate_id(true);

        $_SESSION['flash_success'] = "Vous avez été déconnecté.";
        //if (function_exists('log_console')) log_console('Logout: user data cleared', 'info');

        header("Location: index.php?controller=Redirection&action=openHomepage");
        exit;
    }


    /**
     * Mot de passe oublié : envoi de lien avec token valide 60 min
     */
    public function forgot()
    {
        if (!isset($_POST['forgotPwd'])) {
            header("Location: index.php?controller=Redirection&action=openForgotPwd");
            exit;
        }

        $email   = trim($_POST['email'] ?? '');
        $prModel = new PasswordResetModel();

        // Réponse générique (ne pas révéler si un mail existe ou pas)
        if (!$this->userModel->emailExists($email)) {
            $_SESSION['flash_success'] = "Si l'email existe, un lien de réinitialisation vous a été envoyé.";
            //if (function_exists('log_console')) log_console("Forgot: email inconnu ($email)", 'info');
            header("Location: index.php?controller=Redirection&action=openForgotPwd");
            exit;
        }

        $token = $prModel->createTokenForEmail($email, 60);
        if (!$token) {
            $_SESSION['flash_error'] = "Impossible de générer le lien. Veuillez réessayer.";
            //if (function_exists('log_console')) log_console("Forgot: échec génération token ($email)", 'error');
            header("Location: index.php?controller=Redirection&action=openForgotPwd");
            exit;
        }

        $scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $resetLink = $scheme . '://' . $host . '/index.php?controller=Redirection&action=openChangePwd&token='
            . urlencode($token);

        $to      = $email;
        $subject = 'Réinitialisation du mot de passe';
        $message = '
<div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="center">
    <table width="600" style="background:#ffffff; padding:20px; border-radius:8px; 
    box-shadow:0 4px 8px rgba(0,0,0,0.1);">
      <tr><td style="text-align:center;">
        <h2 style="color:#333333;">Réinitialisation du mot de passe</h2>
        <p style="font-size:16px; color:#555555;">Bonjour,</p>
        <p style="font-size:16px; color:#555555;">
        Pour réinitialiser votre mot de passe, veuillez cliquer sur le bouton ci-dessous :</p>
        <p style="margin: 20px 0;">
          <a href="' . htmlspecialchars($resetLink) . '" 
          style="display:inline-block; padding:10px 20px; background:#007bff; color:#fff; text-decoration:none; 
          border-radius:5px; font-weight:bold;">
            Réinitialiser mon mot de passe
          </a>
        </p>
        <p style="font-size:14px; color:#888888;">Ce lien expire dans 60 minutes.</p>
        <p style="font-size:14px; color:#888888;">
        Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
      </td></tr>
    </table>
  </td></tr></table>
</div>';

        if (Mailer::send($to, $subject, $message)) {
            $_SESSION['flash_success'] = "Si l'email existe, un lien de réinitialisation vous a été envoyé.";
            //if (function_exists('log_console')) log_console("Forgot: mail envoyé ($email)", 'ok');
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi du mail. Veuillez réessayer.";
            //if (function_exists('log_console')) log_console("Forgot: échec envoi mail ($email)", 'error');
        }

        header("Location: index.php?controller=Redirection&action=openForgotPwd");
        exit;
    }

    /**
     * Changement de mot de passe via token
     */
    public function changePwd()
    {
        $prModel = new PasswordResetModel();

        // Affichage du formulaire via le lien GET
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $token = $_GET['token'] ?? '';
            //if (function_exists('log_console')) log_console("GET: Tentative d'accès avec token: " . $token, 'info');
            if (empty($token)) {
                header("Location: index.php?controller=Redirection&action=openHomepage");
                exit;
            }

            $tokenRow = $prModel->getValidTokenRow($token);
            if (function_exists('log_console')) {
                if ($tokenRow) {
                    log_console('GET - Token valide trouvé pour: ' . ($tokenRow['email'] ?? 'N/A'), 'ok');
                } else {
                    log_console('GET - ERREUR: Token non valide/expiré pour token: ' . $token, 'error');
                }
            }

            //$tokenRow = $prModel->getValidTokenRow($token);
            if (!$tokenRow) {
                $_SESSION['flash_error'] = "Lien de réinitialisation invalide ou expiré. Veuillez refaire une demande.";
                header("Location: index.php?controller=Redirection&action=openForgotPwd");
                exit;
            }

            // Affiche la vue avec le token (le form doit contenir un input hidden 'token')
            ViewHandler::show("../view/changePwdView", ['token' => $token]);
            return;
        }

        // Traitement du POST
        if (isset($_POST['changePwd'])) {
            $newPassword     = $_POST['new_password']     ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $token           = $_POST['token']            ?? '';

            //if (function_exists('log_console')) log_console("POST: Soumission avec token: " . $token, 'info');

            $tokenRow = $prModel->getValidTokenRow($token);

            // Log de l'état du jeton pour le bloc POST
            if (function_exists('log_console')) {
                if (!$tokenRow) {
                    log_console('POST - ERREUR: Token invalide/expiré pendant la soumission.', 'error');
                }
            }
            if (!$tokenRow) {
                $_SESSION['flash_error'] = "Lien de réinitialisation invalide ou expiré.";
                //if (function_exists('log_console')) log_console('ChangePwd: token invalide/expiré', 'error');
                header("Location: index.php?controller=Redirection&action=openForgotPwd");
                exit;
            }

            if (strlen($newPassword) < 8) {
                $_SESSION['flash_error'] = "Votre mot de passe n'est pas assez long : minimum 8 caractères.";
                //if (function_exists('log_console')) log_console('ChangePwd: mot de passe < 8', 'error');
                header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
                exit;
            }
            if (
                !preg_match('/[A-Z]/', $newPassword) ||
                !preg_match('/[a-z]/', $newPassword) ||
                !preg_match('/[0-9]/', $newPassword) ||
                !preg_match('/[^a-zA-Z0-9]/', $newPassword)
            ) {
                $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule,
                une minuscule, un chiffre et un caractère spécial.";
                //if (function_exists('log_console')) log_console('ChangePwd: complexité insuffisante', 'error');
                header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
                exit;
            }
            if (empty($newPassword) || empty($confirmPassword)) {
                $_SESSION['flash_error'] = "Veuillez remplir les deux champs de mot de passe.";
                //if (function_exists('log_console')) log_console('ChangePwd: champs vides', 'error');
                header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
                exit;
            }
            if ($newPassword !== $confirmPassword) {
                $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";
                //if (function_exists('log_console')) log_console('ChangePwd: mots de passe différents', 'error');
                header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
                exit;
            }

            $email = $tokenRow['email'] ?? null;
            $ok = $email ? $this->userModel->changePwd($newPassword, $email) : false;

            if ($ok) {
                $prModel->markTokenUsed($token);
                $_SESSION['flash_success'] = "Votre mot de passe a été modifié avec succès.
                 Vous pouvez maintenant vous connecter.";
                //if (function_exists('log_console')) log_console("ChangePwd: succès ($email)", 'ok');
                header("Location: index.php?controller=Redirection&action=openFormConnection");
                exit;
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la modification du mot de passe.";
                //if (function_exists('log_console')) log_console("ChangePwd: échec ($email)", 'error');
                header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
                exit;
            }

            header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
            exit;
        }
    }

    /**
     * Page compte (suppression)
     */
    public function account()
    {
        if (isset($_POST['delete'])) {
            $email = $_SESSION['email'] ?? null;

            if ($email && $this->userModel->delete($email)) {
                //if (function_exists('log_console')) log_console("Account: suppression utilisateur ($email)", 'file');
                session_destroy();
                $_SESSION['flash_success'] = "Votre compte a été supprimé.";
                header("Location: index.php?controller=Redirection&action=openHomepage");
                exit;
            }

            $_SESSION['flash_error'] = "Une erreur est survenue lors de la suppression de votre compte.";
            //if (function_exists('log_console')) log_console("Account: échec suppression ($email)", 'error');
        }

        header("Location: index.php?controller=Redirection&action=openAccount");
        exit;
    }
}
