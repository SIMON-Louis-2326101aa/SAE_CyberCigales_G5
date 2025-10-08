<?php
require_once __DIR__ . '/../model/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function passforgotten() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $user = $this->userModel->findByEmail($email);
            
            if ($user) {
				$message = "Un Email de réinitialisation a été envoyé.";
                require __DIR__ . '/../view/formMdpOublieView.php';
            } else {
                $error = "Email non trouvé";
                require __DIR__ . '/../view/formMdpOublieView.php';
            }
        } else {
            require __DIR__ . '/../view/formMdpOublieView.php';
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
