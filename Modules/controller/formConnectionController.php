<?php
require_once __DIR__ . '/../model/formConnectionModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

class formConnectionController
{
    private $formConnectionModel;

    public function __construct() {
        $this->formConnectionModel = new formConnectionModel();
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

                header("Location: index.php?controller=homepage&action=openHomepage");
                exit();
            } else {
                $error = "Email ou mot de passe incorrect.";
                viewHandler::show("../view/formConnectionView", [
                    'error' => $error,
                    'email' => $email
                ]);
                return;
            }
        }
        
        // Pré-remplir les infos si on vient de l'inscription
        $params = [];
        if (isset($_GET['email'])) {
            $params['email'] = $_GET['email'];
        }
        if (isset($_GET['nom'])) {
            $params['nom'] = $_GET['nom'];
        }
        if (isset($_GET['prenom'])) {
            $params['prenom'] = $_GET['prenom'];
        }
        
        viewHandler::show("../view/formConnectionView", $params);
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

        header("Location: index.php?controller=homepage&action=openHomepage");
        exit();
    }
}