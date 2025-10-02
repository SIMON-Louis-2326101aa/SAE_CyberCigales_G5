<?php
require_once __DIR__ . '/../model/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST['email']);
            $password = $_POST['mdp'];
            $confirm = $_POST['confirm_mdp'];

            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                require __DIR__ . '/../view/formulaireInsc.php';
                return;
            }

            if ($this->userModel->findByEmail($email)) {
                $error = "Cet email est déjà utilisé.";
                require __DIR__ . '/../view/formulaireInsc.php';
                return;
            }

            $this->userModel->register($nom, $prenom, $email, $password);

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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
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
    
    public function passforgotten() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            
            if ($this->userModel->findByEmail($email)) {
                session_start();
                header("Location: /login");
                exit;
            } else {
                $error = "Email non existant.";
                require __DIR__ . '/../view/formulaireMdpOublier.php';
            }
        } else {
            require __DIR__ . '/../view/formulaireMdpOublier.php';
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /home");
        exit;
    }

}
