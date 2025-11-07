<?php
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../model/emailVerificationModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

class adminController
{
    private userModel $userModel;
    private emailVerificationModel $emailVerificationModel;

    public function __construct()
    {
        $this->userModel = new userModel();
        $this->emailVerificationModel = new emailVerificationModel();
        if (function_exists('log_console')) {
            log_console('adminController initialisé', 'ok');
        }

        // Si non Admin, pas accès aux pages admin
        if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'escapethecode2025@gmail.com') {
            header("Location: index.php?controller=redirection&action=openHomepage");
            exit;
        }
    }

    //Liste les utilisateurs grace au userListView (sous une forme de tableau)
    public function listUsers()
    {
        $users = $this->userModel->getAllUsers();
        $pendingUsers = $this->emailVerificationModel->getAllPendingRegistrations();
        viewHandler::show("admin/userListView", ["users" => $users, "pendingUsers" => $pendingUsers]);
    }

    //Permet de modifier les informations lié aux utilisateur (nom, prénom, email) via userEditView
    public function editUser()
    {
        $userId = $_GET['id'] ?? null;

        // Bloquage de la modification du compte Admin
        if ($userId == 5) {
            $_SESSION['flash_error'] = 'Le compte administrateur ne peut pas être modifié.';
            header("Location: index.php?controller=admin&action=listUsers");
            exit;
        }

        //Si l'ID n'existe pas, alors on revient à la liste des utilisateurs
        if (!$userId) {
            header("Location: index.php?controller=admin&action=listUsers");
            exit;
        }

        //Si on modifie des informations
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';

            //Mise à jour des informations
            $this->userModel->updateUser((int)$userId, $nom, $prenom, $email);

            $_SESSION['flash_success'] = "L'utilisateur a été modifié avec succès.";
            header("Location: index.php?controller=admin&action=listUsers");
            exit;
        } else {
            //Sinon Affichage actuelle des informations
            $user = $this->userModel->getUserById((int)$userId);
            if (!$user) {
                header("Location: index.php?controller=admin&action=listUsers");
                exit;
            }
            viewHandler::show("admin/userEditView", ["user" => $user]);
        }
    }

    public function deleteUser()
    {
        $userId = $_GET['id'] ?? null;

        // Bloquage de la suppression du compte Admin
        if ($userId == 5) {
            $_SESSION['flash_error'] = 'Le compte administrateur ne peut pas être supprimé.';
            header("Location: index.php?controller=admin&action=listUsers");
            exit;
        }

        if ($userId) {
            $user = $this->userModel->getUserById((int)$userId);
            if ($user) {
                $this->userModel->delete($user['email']);
                $_SESSION['flash_success'] = "L'utilisateur a été supprimé avec succès.";
            }
        }

        header("Location: index.php?controller=admin&action=listUsers");
        exit;
    }

    public function approveRegistration()
    {
        $pendingId = $_GET['id'] ?? null;
        if ($pendingId) {
            $pendingUser = $this->emailVerificationModel->getPendingRegistrationById((int)$pendingId);
            if ($pendingUser) {
                $this->userModel->createUserAfterVerification($pendingUser['email']);
            }
        }

        header("Location: index.php?controller=admin&action=listUsers");
        exit;
    }

    public function deleteRegistration()
    {
        $pendingId = $_GET['id'] ?? null;
        if ($pendingId) {
            $this->emailVerificationModel->deletePendingRegistrationById((int)$pendingId);
        }

        header("Location: index.php?controller=admin&action=listUsers");
        exit;
    }
}