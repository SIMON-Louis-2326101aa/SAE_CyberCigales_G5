<?php
require_once __DIR__ . '/../model/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['mdp'];
            $confirm = $_POST['confirm_mdp'];

            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                require __DIR__ . '/../view/formulaireInsc.php';
                return;
            }

            if ($this->userModel->findByEmail($email)) {
                $error = "Cet email existe déjà.";
                require __DIR__ . '/../view/formulaireInsc.php';
                return;
            }

            $this->userModel->register($email, $password);
            header("Location: /login");
            exit;
        } else {
            require __DIR__ . '/../view/formulaireInsc.php';
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['mdp'];

            $user = $this->userModel->login($email, $password);
            if ($user) {
                session_start();
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                header("Location: /home");
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
                require __DIR__ . '/../view/formulaireConnection.php';
            }
        } else {
            require __DIR__ . '/../view/formulaireConnection.php';
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login");
        exit;
    }
}
