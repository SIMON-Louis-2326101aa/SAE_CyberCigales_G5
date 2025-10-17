<?php
require_once __DIR__ . '/../model/formRegisterModel.php';
require_once __DIR__ . '/../model/formConnectionModel.php';
require_once __DIR__ . "/../model/forgotPwdModel.php";
require_once __DIR__ . "/../model/changePwdModel.php";
require_once __DIR__ . "/../model/accountModel.php";
require_once __DIR__ . '/../../includes/viewHandler.php';
class userController
{
    private $formInscriptionModel;
    private $formConnectionModel;
    private $forgotPwdModel;
    private $changePwdModel;
    private $accountModel;
    public function __construct() {
        $this->formInscriptionModel = new formRegisterModel();
        $this->formConnectionModel = new formConnectionModel();
        $this->forgotPwdModel = new forgotPwdModel();
        $this->changePwdModel = new changePwdModel();
        $this->accountModel = new accountModel();
    }
    public function register() {
        if (isset($_POST['register'])) {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['pwd'] ?? '';
            $confirm = $_POST['confirm_pwd'] ?? '';

            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                header("Location: /index.php?controller=redirection&action=openFormRegister");
                echo $error;
                return;
            }

            if ($this->formInscriptionModel->findByEmail($email)) {
                $error = "Impossible de créer le compte. Veuillez vérifier les informations saisies.";
                header("Location: /index.php?controller=redirection&action=openFormRegister");
                echo $error;
                return;
            }

            $success = $this->formInscriptionModel->register($nom, $prenom, $email, $password);

            if ($success) {
                // Connexion automatique après inscription
                require_once(__DIR__ . '/../model/formConnectionModel.php');// Inclure le modèle si nécessaire
                $connexionModel = new formConnectionModel();
                $utilisateur = $connexionModel->authenticate($email, $password);

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

                    header("Location: /index.php?controller=redirection&action=openHomepage");
                    exit();
                } else {
                    // Fallback : utilisateur non retrouvé
                    $error = "Inscription réussie, mais problème de connexion automatique.";
                    header("Location: /index.php?controller=redirection&action=openFormConnection");
                    echo $error;
                    return;
                }
            } else {
                $error = "Erreur lors de l'inscription.";
                header("Location: /index.php?controller=redirection&action=openFormRegister");
                echo $error;
                return;
            }
        }
        header("Location: /index.php?controller=redirection&action=openFormRegister");
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['pwd'] ?? '';

            $utilisateur = $this->formConnectionModel->authenticate($email, $password);

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
            $email = $_POST['email'] ?? '';
            if (!$this->forgotPwdModel->emailExists($email)) {
                $data['error'] = "L'email n'existe pas ! Veuillez retourner en arriere pour vous inscrire.";
                echo $data['error'];
            } else {
                $_SESSION['email'] = $email;

                $to = $email;
                $subject = 'Reinitialisation du mot de passe';
                $message = 'Bonjour ! 
                Pour reinitialiser votre mot de passe cliquer sur le lien suivant: 
                https://escapethecode.alwaysdata.net/index.php?controller=redirection&action=openChangePwd';

                if (mail($to, $subject, $message)) {
                    $data['success'] = "Un lien de réinitialisation vous a été envoyé.";
                    echo $data['success'];
                } else {
                    $data['error'] = "Erreur lors de l'envoie du mail. Veuillez réessayer.";
                    echo $data['error'];
                }
            }
        }
        header("Location: index.php?controller=redirection&action=openForgotPwd");

    }
    public function changePwd(){
        if (isset($_POST['changePwd'])) {
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $email = $_SESSION['email'];

            if (empty($newPassword) || empty($confirmPassword)) {
                $data['error'] = "Veuillez remplir les deux champs de mot de passe.";
                echo $data['error'];
            } elseif ($newPassword !== $confirmPassword) {
                $data['error'] = "Les mots de passe ne correspondent pas.";
                echo $data['error'];
            } else {
                if ($this->changePwdModel->changePwd($newPassword, $email)) {
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
            if ($this->accountModel->delete($email)) {
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
