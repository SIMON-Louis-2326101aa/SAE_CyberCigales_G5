<?php
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

class adminController
{
    private userModel $userModel;

    public function __construct()
    {
        $this->userModel = new userModel();
        if (function_exists('log_console')) {
            log_console('adminController initialisé', 'ok');
        }

        // Security check for the whole controller
        if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'escapethecode2025@gmail.com') {
            header("Location: index.php?controller=redirection&action=openHomepage");
            exit;
        }
    }

    public function listUsers()
    {
        $users = $this->userModel->getAllUsers();
        viewHandler::show("admin/userListView", ["users" => $users]);
    }

    public function editUser()
    {
        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            header("Location: index.php?controller=admin&action=listUsers");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';

            $this->userModel->updateUser((int)$userId, $nom, $prenom, $email);

            header("Location: index.php?controller=admin&action=listUsers");
            exit;
        } else {
            $user = $this->userModel->getUserById((int)$userId);
            if (!$user) {
                header("Location: index.php?controller=admin&action=listUsers");
                exit;
            }
            viewHandler::show("admin/userEditView", ["user" => $user]);
        }
    }
}