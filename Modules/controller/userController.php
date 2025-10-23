<?php
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';
require_once __DIR__ . '/../model/emailVerificationModel.php';
require_once __DIR__ . '/../../includes/mailer.php';

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

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['pwd'] ?? '';
        $confirm = $_POST['confirm_pwd'] ?? '';

        $emailModel = new emailVerificationModel();

        // Vérif basique mots de passe
        if ($password !== $confirm) {
            $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";
            if (function_exists('log_console')) log_console('Register: mots de passe non identiques', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openFormRegister");
            //viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription', 'error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['flash_error'] = "Votre mot de passe n'est pas assez long : minimum 8 caractères.";
            if (function_exists('log_console')) log_console('Register: mot de passe < 8 caractères', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openFormRegister");
            //viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription', 'error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);
            exit;
        }

        // Règles de complexité
        $verif_majuscule = '/[A-Z]/'; // Au moins une majuscule
        $verif_minuscule = '/[a-z]/'; // Au moins une minuscule
        $verif_chiffre = '/[0-9]/';   // Au moins un chiffre
        $verif_special = '/[^a-zA-Z0-9]/'; // Au moins un caractère spécial (non alpha-numérique)

        if (
            !preg_match($verif_majuscule, $password) ||
            !preg_match($verif_minuscule, $password) ||
            !preg_match($verif_chiffre, $password) ||
            !preg_match($verif_special, $password)
        ) {
            $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            if (function_exists('log_console')) log_console('Register: complexité mot de passe insuffisante', 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openFormRegister");
            //viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription','error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);
            exit;
        }

        // E-mail déjà utilisé ?
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['flash_error'] = "Impossible de créer le compte. Veuillez vérifier les informations saisies.";
            if (function_exists('log_console')) log_console("Register: email déjà utilisé ($email)", 'info'); // ℹ️
            header("Location: index.php?controller=redirection&action=openFormRegister");
            exit;
        }
        // Vérifier le statut de l'email
        $emailStatus = $this->userModel->getEmailStatus($email);

        // Stocker l'inscription en attente au lieu de créer le compte immédiatement
        $emailModel = new emailVerificationModel();

        if ($emailStatus['pending']) {
            // Si déjà en attente, renvoyer un nouveau code et afficher la vue de vérification
            $code = $emailModel->generateAndStoreCode($email);
            $subject = 'Vérification de votre adresse email';
            // HTML pour le corps de l'email (avec CSS inline)
            $message = '
<div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="text-align: center;">
                            <h2 style="color: #333333;">Vérification de votre adresse email</h2>
                            <p style="font-size: 16px; color: #555555;">Merci de vous être inscrit !</p>
                            <p style="font-size: 16px; color: #555555;">Votre code de vérification est :</p>
                            <p style="font-size: 24px; font-weight: bold; color: #007bff; background-color: #e9f7ff; padding: 10px; border-radius: 4px; display: inline-block;">' . htmlspecialchars($code) . '</p>
                            <p style="font-size: 14px; color: #888888;">Ce code expire dans 10 minutes.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>';
            $sent = Mailer::send($email, $subject, $message);

            $params = ['email' => $email];
            if ($sent) {
                $params['info'] = 'Un nouveau code vous a été envoyé.';
            } else {
                if (class_exists('Constant') && Constant::isDev()) {
                    $params['info'] = "Envoi d'email indisponible en local. Utilisez le code affiché ci-dessous.";
                    $params['devCode'] = $code;
                } else {
                    $params['error'] = "L'envoi de l'email a échoué. Veuillez réessayer plus tard.";
                }
            }
            header("Location: index.php?controller=redirection&action=openEmailVerification");
            return;
        }
        // Stocker l'inscription en attente
        $success = $emailModel->storePendingRegistration($nom, $prenom, $email, password_hash($password, PASSWORD_BCRYPT));

        if ($success) {
            // Générer et envoyer le code, puis afficher la vue de vérification (ne pas auto-login)
            $code = $emailModel->generateAndStoreCode($email);

            $subject = 'Vérification de votre adresse email';
            $message = '
<div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="text-align: center;">
                            <h2 style="color: #333333;">Vérification de votre adresse email</h2>
                            <p style="font-size: 16px; color: #555555;">Merci de vous être inscrit !</p>
                            <p style="font-size: 16px; color: #555555;">Votre code de vérification est :</p>
                            <p style="font-size: 24px; font-weight: bold; color: #007bff; background-color: #e9f7ff; padding: 10px; border-radius: 4px; display: inline-block;">' . htmlspecialchars($code) . '</p>
                            <p style="font-size: 14px; color: #888888;">Ce code expire dans 10 minutes.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>';
            $sent = Mailer::send($email, $subject, $message);
            $params = ['email' => $email];

            if ($sent) {
                $params['info'] = 'Un code vous a été envoyé. Vérifiez votre boîte mail.';
            } else {
                if (class_exists('Constant') && Constant::isDev()) {
                    $params['info'] = "Envoi d'email indisponible en local. Utilisez le code affiché ci-dessous.";
                    $params['devCode'] = $code;
                } else {
                    $params['error'] = "L'envoi de l'email a échoué. Veuillez réessayer plus tard.";
                }
            }
            header("Location: index.php?controller=redirection&action=openEmailVerification");
            return;
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'inscription.";
            if (function_exists('log_console')) log_console("Register: échec insertion DB ($email)", 'error'); // ❌
            header("Location: index.php?controller=redirection&action=openFormRegister");
            exit;
//            viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription', 'error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);

        }
//          obsolete

//         //Connexion automatique après inscription
//        $utilisateur = $this->userModel->authenticate($email, $password);
//        if ($utilisateur) {
//            if (session_status() === PHP_SESSION_NONE) {
//                session_start([
//                    'use_strict_mode' => true,
//                    'cookie_httponly' => true,
//                    'cookie_secure'   => true,
//                    'cookie_samesite' => 'None'
//                ]);
//            }
//
//            $_SESSION['utilisateur'] = $utilisateur;
//            $_SESSION['user_id']     = $utilisateur['id'] ?? null;
//            $_SESSION['nom']         = $utilisateur['nom'] ?? null;
//            $_SESSION['prenom']      = $utilisateur['prenom'] ?? null;
//            $_SESSION['email']       = $utilisateur['email'] ?? null;
//
//            if (function_exists('log_console')) log_console("Register: inscription + login OK ($email)", 'ok'); // ✅
//            header("Location: index.php?controller=redirection&action=openHomepage");
//            exit;
//        }

        header("Location: index.php?controller=redirection&action=openFormRegister");
    }

    // Fallback : inscrit mais pas loggé
    //$_SESSION['flash_error'] = "Inscription réussie, mais problème de connexion automatique.";
    //if (function_exists('log_console')) log_console("Register: login auto échoué ($email)", 'error'); // ❌
    //header("Location: index.php?controller=redirection&action=openFormConnection");
    //exit;

    /**
     * Connexion
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
            header("Location: index.php?controller=redirection&action=openFormConnection");
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['pwd'] ?? '';

        $utilisateur = $this->userModel->authenticate($email, $password);


        if ($utilisateur) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start([
                    'use_strict_mode' => true,
                    'cookie_httponly' => true,
                    'cookie_secure' => true,
                    'cookie_samesite' => 'None'
                ]);
            }

            $_SESSION['utilisateur'] = $utilisateur;
            $_SESSION['user_id'] = $utilisateur['id'] ?? null;
            $_SESSION['nom'] = $utilisateur['nom'] ?? null;
            $_SESSION['prenom'] = $utilisateur['prenom'] ?? null;
            $_SESSION['email'] = $utilisateur['email'] ?? null;

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
        if (isset($_POST['forgotPwd'])) {
            $email = trim($_POST['email'] ?? '');

            if (!$this->userModel->emailExists($email)) {
                $_SESSION['flash_error'] = "Si l'email existe, un lien de réinitialisation vous a été envoyé.";
                if (function_exists('log_console')) log_console("Forgot: email inconnu ($email)", 'info'); // ℹ️
                header("Location: index.php?controller=redirection&action=openForgotPwd");
                exit;
            }

            $prModel = new passwordResetModel();
            $token = $prModel->createTokenForEmail($email, 60); // token valable 60 minutes

            if (!$token) {
                $data['error'] = "Impossible de générer le mail. Réessayez plus tard.";
                echo $data['error'];
                header("Location: index.php?controller=redirection&action=openForgotPwd");
                return;
            }

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $resetLink = $scheme . '://' . $host . '/index.php?controller=redirection&action=openChangePwd&token=' . urlencode($token);

            // Envoi du mail (simple)
            $to = $email;
            $subject = 'Réinitialisation du mot de passe';
            $message = '
            <div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center">
                            <table width="600" style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                <tr>
                                    <td style="text-align: center;">
                                        <h2 style="color: #333333;">Réinitialisation du mot de passe</h2>
                                        <p style="font-size: 16px; color: #555555;">Bonjour,</p>
                                        <p style="font-size: 16px; color: #555555;">Pour réinitialiser votre mot de passe, veuillez cliquer sur le bouton ci-dessous :</p>
                                        <p style="margin: 20px 0;">
                                            <a href="' . htmlspecialchars($resetLink) . '" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                                Réinitialiser mon mot de passe
                                            </a>
                                        </p>
                                        <p style="font-size: 14px; color: #888888;">Ce lien expire dans 60 minutes.</p>
                                        <p style="font-size: 14px; color: #888888;">Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>';
            if (Mailer::send($to, $subject, $message)) {
                $_SESSION['flash_success'] = "Si l'email existe, un lien de réinitialisation vous a été envoyé.";
                if (function_exists('log_console')) log_console("Forgot: mail envoyé ($email)", 'ok'); // ✅
            } else {
                $_SESSION['flash_error'] = "Erreur lors de l'envoi du mail. Veuillez réessayer.";
                if (function_exists('log_console')) log_console("Forgot: échec envoi mail ($email)", 'error'); // ❌
            }

            header("Location: index.php?controller=redirection&action=openForgotPwd");
            exit;
        }
    }


    /**
     * Changement de mot de passe
     */
    public function changePwd()
    {
        // Si on arrive via le lien (GET) : afficher la vue avec le token (la vue doit inclure un champ hidden 'token')
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $token = $_GET['token'] ?? '';
            if (empty($token)) {
                header("Location: index.php?controller=redirection&action=openHomepage");
                exit();
            }
            // Afficher la vue changePwdView et fournir le token (la vue doit mettre le token dans le form)
            viewHandler::show("../view/changePwdView", ['token' => $token]);
            return;
        }

        // Traitement du POST pour changer le mot de passe
        if (isset($_POST['changePwd'])) {
            $newPassword      = $_POST['new_password'] ?? '';
            $confirmPassword  = $_POST['confirm_password'] ?? '';
            $token = $_POST['token'] ?? '';

            $prModel = new passwordResetModel();
            $tokenRow = $prModel->getValidTokenRow($token);
            if (!$tokenRow) {
                $data['error'] = "Lien invalide ou expiré.";
                echo $data['error'];
                header("Location: /index.php?controller=redirection&action=openForgotPwd");
                return;
            }

            if (empty($token)) {
                $data['error'] = "Token manquant.";
                echo $data['error'];
                header("Location: /index.php?controller=redirection&action=openChangePwd");
                return;
            }

            if (strlen($newPassword) < 8) {
                $_SESSION['flash_error'] = "Votre mot de passe n'est pas assez long : minimum 8 caractères.";
                if (function_exists('log_console')) log_console('ChangePwd: mot de passe < 8', 'error'); // ❌
                header("Location: index.php?controller=redirection&action=openChangePwd");
                //viewHandler::show("../view/changePwdView", ['token' => $token]);
                exit;
            }

            $verif_majuscule = '/[A-Z]/';
            $verif_minuscule = '/[a-z]/';
            $verif_chiffre = '/[0-9]/';
            $verif_special = '/[^a-zA-Z0-9]/';

            if (
                !preg_match($verif_majuscule, $newPassword) ||
                !preg_match($verif_minuscule, $newPassword) ||
                !preg_match($verif_chiffre, $newPassword) ||
                !preg_match($verif_special, $newPassword)
            ) {
                $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                if (function_exists('log_console')) log_console('ChangePwd: complexité insuffisante', 'error'); // ❌
                header("Location: index.php?controller=redirection&action=openChangePwd");
                //viewHandler::show("../view/changePwdView", ['token' => $token]);
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
            $email = $tokenRow['email'];

            $ok = $this->userModel->changePwd($newPassword, $email);
            if ($ok) {
                $prModel->markTokenUsed($token);
                $_SESSION['flash_success'] = "Votre mot de passe a été modifié avec succès.";
                if (function_exists('log_console')) log_console("ChangePwd: succès ($email)", 'ok'); // ✅
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la modification du mot de passe.";
                if (function_exists('log_console')) log_console("ChangePwd: échec ($email)", 'error'); // ❌

            }
            header("Location: index.php?controller=redirection&action=openChangePwd");
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
