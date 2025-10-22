<?php
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';
require_once __DIR__ . '/../model/emailVerificationModel.php';
require_once __DIR__ . '/../../includes/mailer.php';
class userController
{
    private $userModel;
    public function __construct() {
        $this->userModel = new userModel();
    }
    public function register() {
        if (isset($_POST['register'])) {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['pwd'] ?? '';
            $confirm = $_POST['confirm_pwd'] ?? '';

            $emailModel = new emailVerificationModel();


            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                // On affiche la vue avec l'erreur et les données conservées
                viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription', 'error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);
                return;
            }

            if(strlen($password) < 8) {
                $error = "Votre mot de passe n'est pas assez long : minimun 8 caractères";
                // Modification ici
                viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription', 'error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);
                return;
            }

            $verif_majuscule = '/[A-Z]/'; // Au moins une majuscule
            $verif_minuscule = '/[a-z]/'; // Au moins une minuscule
            $verif_chiffre = '/[0-9]/';   // Au moins un chiffre
            $verif_special = '/[^a-zA-Z0-9]/'; // Au moins un caractère spécial (non alpha-numérique)

            if (!preg_match($verif_majuscule, $password) ||
                !preg_match($verif_minuscule, $password) ||
                !preg_match($verif_chiffre, $password) ||
                !preg_match($verif_special, $password))
            {
                $error = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                // Modification ici
                viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription','error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);
                return;
            }

            // Vérifier le statut de l'email
            $emailStatus = $this->userModel->getEmailStatus($email);

            if ($emailStatus['verified']) {
                // Le compte existe et est vérifié
                $error = "Inscription imposible .";
                // Modification ici
                viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription', 'error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);
                return;
            }

            // Stocker l'inscription en attente au lieu de créer le compte immédiatement
            $emailModel = new emailVerificationModel();

            if ($emailStatus['pending']) {
                // Si déjà en attente, renvoyer un nouveau code et afficher la vue de vérification
                $code = $emailModel->generateAndStoreCode($email);

                $subject = 'Vérification de votre adresse email';
                $message = "Votre code de vérification est : {$code}\nIl expire dans 10 minutes.";
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

                viewHandler::show('../view/emailVerificationView', $params);
                return;
            }

            // Stocker l'inscription en attente (nouvelle inscription)
            $success = $emailModel->storePendingRegistration($nom, $prenom, $email, password_hash($password, PASSWORD_BCRYPT));

            if ($success) {
                // Générer et envoyer le code, puis afficher la vue de vérification (ne pas auto-login)
                $code = $emailModel->generateAndStoreCode($email);

                $subject = 'Vérification de votre adresse email';
                $message = "Votre code de vérification est : {$code}\nIl expire dans 10 minutes.";
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

                viewHandler::show('../view/emailVerificationView', $params);
                return;
            } else {
                $error = "Erreur lors de l'inscription.";
                // Modification ici
                viewHandler::show("../view/formRegisterView", ['pageTitle' => 'Inscription', 'error' => $error, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email]);
                return;
            }
        }
        header("Location: index.php?controller=redirection&action=openFormRegister");
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['pwd'] ?? '';

            $utilisateur = $this->userModel->authenticate($email, $password);

            if ($utilisateur) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start([
                        'use_strict_mode' => true,
                        'cookie_httponly' => true,
                        'cookie_secure' => true,
                        'cookie_samesite' => 'None'
                    ]);
                }

                $_SESSION['utilisateur'] = $utilisateur;
                $_SESSION['user_id'] = $utilisateur['id'];
                $_SESSION['nom'] = $utilisateur['nom'];
                $_SESSION['prenom'] = $utilisateur['prenom'];
                $_SESSION['email'] = $utilisateur['email'];

                header("Location: index.php?controller=redirection&action=openHomepage");
                exit();
            } else {
                $error = "Email ou mot de passe incorrect.";
                header("Location: index.php?controller=redirection&action=openFormConnection");
                echo $error;
                return;
            }
        }
        header("Location: index.php?controller=redirection&action=openFormConnection");
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();

        header("Location: index.php?controller=redirection&action=openHomepage");
        exit();
    }

    public function forgot(){
        if (isset($_POST['forgotPwd'])) {
            $email = trim($_POST['email'] ?? '');

            if (!$this->userModel->emailExists($email)) {
                $data['error'] = "L'email n'existe pas ! Veuillez retourner en arrière pour vous inscrire.";
                echo $data['error'];
                header("Location: index.php?controller=redirection&action=openForgotPwd");
                return;
            }

            $prModel = new passwordResetModel();
            $token = $prModel->createTokenForEmail($email, 60); // token valable 60 minutes

            if (!$token) {
                $data['error'] = "Impossible de générer le token. Réessayez plus tard.";
                echo $data['error'];
                header("Location: index.php?controller=redirection&action=openForgotPwd");
                return;
            }

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $resetLink = $scheme . '://' . $host . '/index.php?controller=redirection&action=openChangePwd&token=' . urlencode($token);

            $to = $email;
            $subject = 'Réinitialisation du mot de passe';
            $message = "Bonjour,\n\nPour réinitialiser votre mot de passe, cliquez sur le lien suivant :\n\n{$resetLink}\n\nLe lien expire dans 60 minutes.\n\nSi vous n'avez pas demandé cette réinitialisation, ignorez ce message.";

            if (Mailer::send($to, $subject, $message)) {
                $data['success'] = "Un lien de réinitialisation vous a été envoyé.";
                echo $data['success'];
            } else {
                if (class_exists('Constant') && Constant::isDev()) {
                    // Afficher le token en local pour faciliter les tests
                    $data['info'] = "Envoi d'email indisponible en local. Token: {$token}";
                    echo $data['info'];
                } else {
                    $data['error'] = "Erreur lors de l'envoi du mail. Veuillez réessayer.";
                    echo $data['error'];
                }
            }
        }
        header("Location: index.php?controller=redirection&action=openForgotPwd");
    }

    public function changePwd(){
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
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $token = $_POST['token'] ?? '';

            if (empty($token)) {
                $data['error'] = "Token manquant.";
                echo $data['error'];
                header("Location: /index.php?controller=redirection&action=openChangePwd");
                return;
            }

            if(strlen($newPassword) < 8) {
                $error = "Votre mot de passe n'est pas assez long : minimum 8 caractères";
                viewHandler::show("../view/changePwdView", ['token' => $token]);
                echo $error;
                return;
            }

            $verif_majuscule = '/[A-Z]/';
            $verif_minuscule = '/[a-z]/';
            $verif_chiffre = '/[0-9]/';
            $verif_special = '/[^a-zA-Z0-9]/';

            if (!preg_match($verif_majuscule, $newPassword) ||
                !preg_match($verif_minuscule, $newPassword) ||
                !preg_match($verif_chiffre, $newPassword) ||
                !preg_match($verif_special, $newPassword))
            {
                $error = "Le mot de passe doit contenir au moins : 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                viewHandler::show("../view/changePwdView", ['token' => $token]);
                echo $error;
                return;
            }

            if (empty($newPassword) || empty($confirmPassword)) {
                $data['error'] = "Veuillez remplir les deux champs de mot de passe.";
                echo $data['error'];
            } elseif ($newPassword !== $confirmPassword) {
                $data['error'] = "Les mots de passe ne correspondent pas.";
                echo $data['error'];
            } else {
                $prModel = new passwordResetModel();
                $tokenRow = $prModel->getValidTokenRow($token);
                if (!$tokenRow) {
                    $data['error'] = "Token invalide ou expiré.";
                    echo $data['error'];
                    header("Location: /index.php?controller=redirection&action=openForgotPwd");
                    return;
                }

                $email = $tokenRow['email'];

                if ($this->userModel->changePwd($newPassword, $email)) {
                    $prModel->markTokenUsed($token);
                    $data['success'] = "Votre mot de passe a été modifié avec succès.";
                    echo $data['success'];
                } else {
                    $data['error'] = "Erreur lors de la modification du mot de passe.";
                    echo $data['error'];
                }
            }
        }
        header("Location: /index.php?controller=redirection&action=openChangePwd");
    }
    public function account(){

        if (isset($_POST['delete'])) {
            $email = $_SESSION['email'];
            if ($this->userModel->delete($email)) {
                session_destroy();
                header("Location: /index.php?controller=redirection&action=openHomepage");
                exit();
            } else {
                $data['error'] = "Une erreur est survenue lors de la suppression de votre compte.";
                echo $data['error'];
            }
        }

        header("Location: /index.php?controller=redirection&action=openAccount");
    }
}
