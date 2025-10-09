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

            if (empty($email) || empty($password)) {
                $error = "Veuillez remplir tous les champs.";
                viewHandler::show("../view/formConnectionView", ['error' => $error]);
                echo $error;
                return;
            }

            if (!$this->formConnectionModel->emailExists($email)) {
                $data['error'] = "L'email n'existe pas ! Veuillez vous inscrire.";
                echo $data['error'];
            }else {
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

                    // Stocker les informations dans la session
                    $_SESSION['utilisateur'] = $utilisateur;
                    $_SESSION['user_id'] = $utilisateur['id'];
                    $_SESSION['nom'] = $utilisateur['nom'];
                    $_SESSION['prenom'] = $utilisateur['prenom'];
                    $_SESSION['email'] = $utilisateur['email'];

                    header("Location: index.php?controller=homepage&action=openHomepage");
                    exit();
                } else {
                    $error = "Mot de passe incorrect.";
                    viewHandler::show("../view/formConnectionView", ['error' => $error]);
                    echo $error;
                    return;
                }
            }
        }
        viewHandler::show("../view/formConnectionView");
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