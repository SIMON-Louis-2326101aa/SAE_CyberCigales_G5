<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\Mailer;
use SAE_CyberCigales_G5\includes\ViewHandler;
use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel;
use SAE_CyberCigales_G5\Modules\model\GameProgressModel;
use SAE_CyberCigales_G5\Modules\model\LoginAttemptModel;
use SAE_CyberCigales_G5\Modules\model\PasswordResetModel;
use SAE_CyberCigales_G5\Modules\model\PendingRegistrationModel;
use SAE_CyberCigales_G5\Modules\model\UserModel;

/**
 * Contrôleur utilisateur
 *
 * Gère toutes les actions liées aux utilisateurs : inscription, connexion,
 * déconnexion, gestion de compte, mot de passe oublié, etc.
 */
class UserController
{
    private UserModel $userModel;
    private LoginAttemptModel $loginAttemptModel;
    private GameProgressModel $gameProgressModel;

    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    private function renderEmailTemplate(string $template, array $data = []): string
    {
        extract($data);

        ob_start();
        require __DIR__ . "/../view/email/{$template}.php";
        return (string)ob_get_clean();
    }

    /**
     * Constructeur du contrôleur
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loginAttemptModel = new LoginAttemptModel();
        $this->gameProgressModel = new GameProgressModel();

        self::log('UserController initialisé', 'ok', [
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);
    }

    /**
     * Inscription utilisateur avec vérification e-mail
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['register'])) {
            $_SESSION['flash_error'] = "Accès invalide au formulaire d'inscription.";

            self::log('Register refusé: mauvaise méthode ou accès direct', 'warn', [
                'method' => $_SERVER['REQUEST_METHOD'] ?? null,
            ]);

            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        $nom      = trim($_POST['nom'] ?? '');
        $prenom   = trim($_POST['prenom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['pwd'] ?? '';
        $confirm  = $_POST['confirm_pwd'] ?? '';

        unset($_SESSION['old']);

        $sanitize = static function (string $s): string {
            $s = strip_tags($s);
            $s = preg_replace('/\s+/', ' ', $s);
            return mb_substr(trim($s), 0, 120);
        };

        $_SESSION['old'] = [
            'nom'    => $sanitize($nom),
            'prenom' => $sanitize($prenom),
            'email'  => $sanitize($email),
        ];

        self::log('Register: soumission reçue', 'file', [
            'email' => $email,
            'has_nom' => $nom !== '',
            'has_prenom' => $prenom !== '',
        ]);

        if ($password !== $confirm) {
            $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";
            self::log('Register: mots de passe différents', 'warn', [
                'email' => $email,
            ]);
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        if (strlen($password) < 12) {
            $_SESSION['flash_error'] = "Votre mot de passe n'est pas assez long : minimum 12 caractères.";
            self::log('Register: mot de passe trop court', 'warn', [
                'email' => $email,
                'length' => strlen($password),
            ]);
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        if (
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^a-zA-Z0-9]/', $password)
        ) {
            $_SESSION['flash_error'] = "Le mot de passe doit contenir au moins : 12 caractères, une majuscule, 
            une minuscule, un chiffre et un caractère spécial.";
            self::log('Register: complexité mot de passe insuffisante', 'warn', [
                'email' => $email,
            ]);
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['flash_error'] = "Impossible de créer le compte. Veuillez vérifier les informations saisies.";
            self::log('Register: email déjà utilisé', 'info', [
                'email' => $email,
            ]);
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        $emailModel   = new EmailVerificationModel();
        $pendingModel = new PendingRegistrationModel();
        $emailStatus  = $this->userModel->getEmailStatus($email);

        if (!empty($emailStatus['pending'])) {
            $code = $emailModel->generateAndStoreCode($email);
            $subject = 'Vérification de votre adresse email';
            $message = $this->renderEmailTemplate('verificationEmail', [
                'code' => $code
            ]);

            $sent = Mailer::send($email, $subject, $message);

            if ($sent) {
                $_SESSION['flash_success'] = "Un nouveau code vous a été envoyé.";
                self::log('Register: renvoi code vérification OK', 'ok', [
                    'email' => $email,
                ]);
            } else {
                $_SESSION['flash_error'] = "L'envoi de l'e-mail a échoué. Veuillez réessayer plus tard.";
                self::log('Register: échec renvoi code vérification', 'error', [
                    'email' => $email,
                ]);
            }

            unset($_SESSION['old']);

            header("Location: index.php?controller=Redirection&action=openEmailVerification&email="
                . urlencode($email));
            exit;
        }

        $stored = $pendingModel->storePendingRegistration(
            $nom,
            $prenom,
            $email,
            password_hash($password, PASSWORD_BCRYPT)
        );

        if (!$stored) {
            $_SESSION['flash_error'] = "Erreur lors de l'inscription.";
            self::log('Register: échec insertion pending registration', 'error', [
                'email' => $email,
            ]);
            header("Location: index.php?controller=Redirection&action=openFormRegister");
            exit;
        }

        $code = $emailModel->generateAndStoreCode($email);
        $subject = 'Vérification de votre adresse email';
        $message = $this->renderEmailTemplate('verificationEmail', [
            'code' => $code
        ]);

        $sent = Mailer::send($email, $subject, $message);

        if ($sent) {
            $_SESSION['flash_success'] = "Un code vous a été envoyé. Vérifiez votre boîte mail.";
            self::log('Register: code vérification envoyé', 'ok', [
                'email' => $email,
            ]);
        } else {
            $_SESSION['flash_error'] = "L'envoi de l'e-mail a échoué. Veuillez réessayer plus tard.";
            self::log('Register: échec envoi code vérification', 'error', [
                'email' => $email,
            ]);
        }

        unset($_SESSION['old']);

        header("Location: index.php?controller=Redirection&action=openEmailVerification&email=" . urlencode($email));
        exit;
    }

    /**
     * Connexion utilisateur
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
            $_SESSION['flash_error'] = "Accès invalide au formulaire de connexion.";

            self::log('Login refusé: mauvaise méthode ou accès direct', 'warn', [
                'method' => $_SERVER['REQUEST_METHOD'] ?? null,
            ]);

            header("Location: index.php?controller=Redirection&action=openFormConnection");
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['pwd'] ?? '';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '';

        self::log('Login: tentative connexion', 'file', [
            'email' => $email,
            'ip' => $ip,
        ]);

        $accountBlocked = $this->loginAttemptModel->isAccountBlocked($email);
        if ($accountBlocked['blocked']) {
            $remainingMinutes = ceil($accountBlocked['remaining_time'] / 60);
            $_SESSION['flash_error'] = "Trop de tentatives de connexion échouées. Veuillez réessayer dans 
            {$remainingMinutes} minute(s).";

            self::log('Login bloqué: compte temporairement verrouillé', 'warn', [
                'email' => $email,
                'remaining_minutes' => $remainingMinutes,
            ]);

            header("Location: index.php?controller=Redirection&action=openFormConnection");
            return;
        }

        $ipBlocked = $this->loginAttemptModel->isIPBlocked($ip);
        if ($ipBlocked['blocked']) {
            $remainingMinutes = ceil($ipBlocked['remaining_time'] / 60);
            $_SESSION['flash_error'] = "Trop de tentatives de connexion depuis cette adresse IP. Veuillez réessayer 
            dans {$remainingMinutes} minute(s).";

            self::log('Login bloqué: IP temporairement verrouillée', 'warn', [
                'email' => $email,
                'ip' => $ip,
                'remaining_minutes' => $remainingMinutes,
            ]);

            header("Location: index.php?controller=Redirection&action=openFormConnection");
            return;
        }

        $utilisateur = $this->userModel->authenticate($email, $password);

        if (!$utilisateur) {
            $this->loginAttemptModel->recordFailedAttempt($email, $ip);

            $accountBlocked = $this->loginAttemptModel->isAccountBlocked($email);
            if ($accountBlocked['blocked']) {
                $remainingMinutes = ceil($accountBlocked['remaining_time'] / 60);
                $blockDuration = $accountBlocked['block_duration'];
                $attempts = $accountBlocked['attempts'];
                $_SESSION['flash_error'] = "Compte temporairement bloqué pour {$remainingMinutes} minute(s). 
                (Tentative {$attempts} - Temps de blocage: {$blockDuration} min)";

                self::log('Login échoué: compte vient d’être bloqué', 'warn', [
                    'email' => $email,
                    'ip' => $ip,
                    'attempts' => $attempts,
                    'block_duration' => $blockDuration,
                ]);
            } else {
                $attempts = $accountBlocked['attempts'];
                $remainingAttempts = 4 - $attempts;

                if ($attempts < 3) {
                    $_SESSION['flash_error'] = "E-mail ou mot de passe incorrect. Il vous reste {$remainingAttempts} 
                    tentative(s) avant le premier blocage.";
                } else {
                    $_SESSION['flash_error'] = "⚠️ E-mail ou mot de passe incorrect. Attention : prochaine tentative
                     échouée = blocage de 1 minute !";
                }

                self::log('Login échoué: authentification invalide', 'info', [
                    'email' => $email,
                    'ip' => $ip,
                    'attempts' => $attempts,
                    'remaining_attempts' => $remainingAttempts,
                ]);
            }

            header("Location: index.php?controller=Redirection&action=openFormConnection");
            exit;
        }

        if ((int)$utilisateur['is_banned'] === 1) {
            $_SESSION['flash_error'] = "Votre compte est banni. Contactez un administrateur.";

            self::log('Login refusé: compte banni', 'warn', [
                'email' => $email,
                'user_id' => $utilisateur['id'] ?? null,
            ]);

            header("Location: index.php?controller=Redirection&action=openFormConnection");
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'use_strict_mode' => true,
                'cookie_httponly' => true,
                'cookie_samesite' => 'None'
            ]);
        }

        $_SESSION['utilisateur'] = $utilisateur;
        $_SESSION['user_id']     = $utilisateur['id'];
        $_SESSION['nom']         = $utilisateur['nom'];
        $_SESSION['prenom']      = $utilisateur['prenom'];
        $_SESSION['email']       = $utilisateur['email'];

        $this->loginAttemptModel->clearFailedAttempts($email);

        $progressModel = new GameProgressModel();
        $progressModel->startOrResumeGame($_SESSION['user_id']);
        $progress = $progressModel->getByUserId($_SESSION['user_id']);
        $_SESSION['gameprogress'] = $progress;

        $_SESSION['flash_success'] = "Connexion réussie.";

        self::log('Login réussi', 'ok', [
            'email' => $email,
            'user_id' => $_SESSION['user_id'],
            'has_progress' => $progress !== null,
        ]);

        header("Location: index.php?controller=Redirection&action=openHomepage");
        exit;
    }

    /**
     * Déconnexion utilisateur
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $email = $_SESSION['email'] ?? null;

        if ($userId !== null) {
            $progressModel = new GameProgressModel();
            $progressModel->pauseGame((int)$userId);
        }

        unset(
            $_SESSION['utilisateur'],
            $_SESSION['user_id'],
            $_SESSION['nom'],
            $_SESSION['prenom'],
            $_SESSION['email']
        );

        session_regenerate_id(true);

        $_SESSION['flash_success'] = "Vous avez été déconnecté.";

        self::log('Logout effectué', 'info', [
            'user_id' => $userId,
            'email' => $email,
        ]);

        header("Location: index.php?controller=Redirection&action=openHomepage");
        exit;
    }

    /**
     * Mot de passe oublié : envoi du lien de réinitialisation
     */
    public function forgot()
    {
        if (!isset($_POST['forgotPwd'])) {
            self::log('Forgot refusé: accès direct', 'warn');
            header("Location: index.php?controller=Redirection&action=openForgotPwd");
            exit;
        }

        $email   = trim($_POST['email'] ?? '');
        $prModel = new PasswordResetModel();

        self::log('Forgot: demande reçue', 'file', [
            'email' => $email,
        ]);

        if (!$this->userModel->emailExists($email)) {
            $_SESSION['flash_success'] = "Si l'e-mail existe, un lien de réinitialisation vous a été envoyé.";

            self::log('Forgot: email inconnu ou non enregistré', 'info', [
                'email' => $email,
            ]);

            header("Location: index.php?controller=Redirection&action=openForgotPwd");
            exit;
        }

        $token = $prModel->createTokenForEmail($email, 60);
        if (!$token) {
            $_SESSION['flash_error'] = "Impossible de générer le lien. Veuillez réessayer.";

            self::log('Forgot: échec génération token', 'error', [
                'email' => $email,
            ]);

            header("Location: index.php?controller=Redirection&action=openForgotPwd");
            exit;
        }

        $scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $resetLink = $scheme . '://' . $host . '/index.php?controller=Redirection&action=openChangePwd&token='
            . urlencode($token);

        $to      = $email;
        $subject = 'Réinitialisation du mot de passe';
        $message = $this->renderEmailTemplate('resetPassword', [
            'resetLink' => $resetLink
        ]);

        if (Mailer::send($to, $subject, $message)) {
            $_SESSION['flash_success'] = "Si l'e-mail existe, un lien de réinitialisation vous a été envoyé.";

            self::log('Forgot: mail réinitialisation envoyé', 'ok', [
                'email' => $email,
            ]);
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi du mail. Veuillez réessayer.";

            self::log('Forgot: échec envoi mail réinitialisation', 'error', [
                'email' => $email,
            ]);
        }

        header("Location: index.php?controller=Redirection&action=openForgotPwd");
        exit;
    }

    /**
     * Changement de mot de passe via token de réinitialisation
     */
    public function changePwd()
    {
        $prModel = new PasswordResetModel();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $token = $_GET['token'] ?? '';

            self::log('ChangePwd GET: accès formulaire', 'file', [
                'has_token' => $token !== '',
            ]);

            if (empty($token)) {
                header("Location: index.php?controller=Redirection&action=openHomepage");
                exit;
            }

            $tokenRow = $prModel->getValidTokenRow($token);

            if ($tokenRow) {
                self::log('ChangePwd GET: token valide', 'ok', [
                    'email' => $tokenRow['email'] ?? null,
                ]);
            } else {
                self::log('ChangePwd GET: token invalide ou expiré', 'warn');
            }

            if (!$tokenRow) {
                $_SESSION['flash_error'] = "Lien de réinitialisation invalide ou expiré. Veuillez refaire une demande.";
                header("Location: index.php?controller=Redirection&action=openForgotPwd");
                exit;
            }

            ViewHandler::show("changePwdView", [
                'token' => $token,
                'pageTitle' => 'Changement de mot de passe'
            ]);
            return;
        }

        if (isset($_POST['changePwd'])) {
            $newPassword     = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $token           = $_POST['token'] ?? '';

            self::log('ChangePwd POST: soumission reçue', 'file', [
                'has_token' => $token !== '',
            ]);

            $tokenRow = $prModel->getValidTokenRow($token);

            if (!$tokenRow) {
                $_SESSION['flash_error'] = "Lien de réinitialisation invalide ou expiré.";

                self::log('ChangePwd POST: token invalide ou expiré', 'warn');

                header("Location: index.php?controller=Redirection&action=openForgotPwd");
                exit;
            }

            if (strlen($newPassword) < 8) {
                $_SESSION['flash_error'] = "Votre mot de passe n'est pas assez long : minimum 8 caractères.";

                self::log('ChangePwd POST: mot de passe trop court', 'warn', [
                    'email' => $tokenRow['email'] ?? null,
                    'length' => strlen($newPassword),
                ]);

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

                self::log('ChangePwd POST: complexité insuffisante', 'warn', [
                    'email' => $tokenRow['email'] ?? null,
                ]);

                header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
                exit;
            }

            if (empty($newPassword) || empty($confirmPassword)) {
                $_SESSION['flash_error'] = "Veuillez remplir les deux champs de mot de passe.";

                self::log('ChangePwd POST: champs vides', 'warn', [
                    'email' => $tokenRow['email'] ?? null,
                ]);

                header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";

                self::log('ChangePwd POST: mots de passe différents', 'warn', [
                    'email' => $tokenRow['email'] ?? null,
                ]);

                header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
                exit;
            }

            $email = $tokenRow['email'] ?? null;
            $ok = $email ? $this->userModel->changePwd($newPassword, $email) : false;

            if ($ok) {
                $prModel->markTokenUsed($token);
                $_SESSION['flash_success'] = "Votre mot de passe a été modifié avec succès.
                 Vous pouvez maintenant vous connecter.";

                self::log('ChangePwd POST: succès modification mot de passe', 'ok', [
                    'email' => $email,
                ]);

                header("Location: index.php?controller=Redirection&action=openFormConnection");
                exit;
            }

            $_SESSION['flash_error'] = "Erreur lors de la modification du mot de passe.";

            self::log('ChangePwd POST: échec modification mot de passe', 'error', [
                'email' => $email,
            ]);

            header("Location: index.php?controller=Redirection&action=openChangePwd&token=" . urlencode($token));
            exit;
        }
    }

    /**
     * Page compte (suppression / reset progression)
     */
    public function account()
    {
        if (isset($_POST['reset_progression'])) {
            $email = $_SESSION['email'] ?? '';

            if ($email === $_ENV['ADMIN_EMAIL']) {
                $_SESSION['flash_error'] = "Action interdite pour l'administrateur.";

                self::log('Account: reset progression refusé pour admin', 'warn', [
                    'email' => $email,
                ]);

                header("Location: index.php?controller=Redirection&action=openAccount");
                exit;
            }

            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                $_SESSION['flash_error'] = "Utilisateur non connecté.";

                self::log('Account: reset progression impossible, utilisateur absent', 'warn');

                header("Location: index.php?controller=Redirection&action=openFormConnection");
                exit;
            }

            $this->gameProgressModel->deleteByUserId((int)$userId);

            unset($_SESSION['team'], $_SESSION['game_start_time']);

            $_SESSION['flash_success'] = "Votre progression a été réinitialisée.";

            self::log('Account: progression réinitialisée', 'ok', [
                'user_id' => (int)$userId,
                'email' => $email,
            ]);

            header("Location: index.php?controller=Redirection&action=openAccount");
            exit;
        }

        if (isset($_POST['delete'])) {
            $email = $_SESSION['email'] ?? null;
            $userId = $_SESSION['user_id'] ?? null;

            if ($email && $this->userModel->delete($email)) {
                self::log('Account: compte supprimé', 'file', [
                    'user_id' => $userId,
                    'email' => $email,
                ]);

                session_destroy();
                $_SESSION['flash_success'] = "Votre compte a été supprimé.";
                header("Location: index.php?controller=Redirection&action=openHomepage");
                exit;
            }

            $_SESSION['flash_error'] = "Une erreur est survenue lors de la suppression de votre compte.";

            self::log('Account: échec suppression compte', 'error', [
                'user_id' => $userId,
                'email' => $email,
            ]);
        }

        header("Location: index.php?controller=Redirection&action=openAccount");
        exit;
    }
}
