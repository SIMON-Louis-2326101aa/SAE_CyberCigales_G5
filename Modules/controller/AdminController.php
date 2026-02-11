<?php

//require_once __DIR__ . '/../model/userModel.php';
//require_once __DIR__ . '/../model/emailVerificationModel.php';
//require_once __DIR__ . '/../../includes/viewHandler.php';

namespace SAE_CyberCigales_G5\Modules\controller ;

use SAE_CyberCigales_G5\includes\ViewHandler;
use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel;
use SAE_CyberCigales_G5\Modules\model\GameProgressModel;
use SAE_CyberCigales_G5\Modules\model\PendingRegistrationModel;
use SAE_CyberCigales_G5\Modules\model\UserModel;

class AdminController
{
    private userModel $userModel;
    private PendingRegistrationModel $pendingRegistrationModel;
    private GameProgressModel $gameProgressModel;
    public function __construct()
    {
        $this->userModel = new userModel();
        $this->pendingRegistrationModel = new PendingRegistrationModel();
        $this->gameProgressModel = new GameProgressModel();
        if (function_exists('log_console')) {
            log_console('AdminController initialisé', 'ok');
        }

        // Si non Admin, pas accès aux pages admin
        if (!isset($_SESSION['email']) || $_SESSION['email'] !== $_ENV['ADMIN_EMAIL']) {
            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }
    }

    //Liste les utilisateurs grace au userListView (sous une forme de tableau)
    public function listUsers()
    {
        $users = $this->userModel->getAllUsers();
        $pendingUsers = $this->pendingRegistrationModel->getAllPendingRegistrations();
        $progressGames = $this->gameProgressModel->getAllGameProgress();
        viewHandler::show("admin/userListView", ["users" => $users, "pendingUsers" => $pendingUsers,
            "progressGames" => $progressGames]);
    }

    //Permet de modifier les informations lié aux utilisateur (nom, prénom, email) via userEditView
    public function editUser()
    {
        $userId = $_GET['id'] ?? null;
        // Bloquage de la modification du compte Admin
        if ($userId == 5) {
            $_SESSION['flash_error'] = 'Le compte administrateur ne peut pas être modifié.';
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        //Si l'ID n'existe pas, alors on revient à la liste des utilisateurs
        if (!$userId) {
            header("Location: index.php?controller=Admin&action=listUsers");
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
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        } else {
        //Sinon Affichage actuelle des informations
            $user = $this->userModel->getUserById((int)$userId);
            if (!$user) {
                header("Location: index.php?controller=Admin&action=listUsers");
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
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        if ($userId) {
            $user = $this->userModel->getUserById((int)$userId);
            if ($user) {
                $this->userModel->delete($user['email']);
                $_SESSION['flash_success'] = "L'utilisateur a été supprimé avec succès.";
            }
        }

        header("Location: index.php?controller=Admin&action=listUsers");
        exit;
    }

    public function approveRegistration()
    {
        $pendingId = $_GET['id'] ?? null;
        if ($pendingId) {
            $pendingUser = $this->pendingRegistrationModel->getPendingRegistrationById((int)$pendingId);
            if ($pendingUser) {
                $this->userModel->createUserAfterVerification($pendingUser['email']);
            }
        }

        header("Location: index.php?controller=Admin&action=listUsers");
        exit;
    }

    public function deleteRegistration()
    {
        $pendingId = $_GET['id'] ?? null;
        if ($pendingId) {
            $this->pendingRegistrationModel->deletePendingRegistrationById((int)$pendingId);
        }

        header("Location: index.php?controller=Admin&action=listUsers");
        exit;
    }

    public function banUser()
    {
        $userId = $_GET['id'] ?? null;

        // Protection compte admin
        if ($userId == 5) {
            $_SESSION['flash_error'] = "Le compte administrateur ne peut pas être banni.";
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        if ($userId) {
            $this->userModel->banUser((int)$userId);
            $_SESSION['flash_success'] = "Utilisateur banni avec succès.";
        }

        header("Location: index.php?controller=Admin&action=listUsers");
        exit;
    }

    public function unbanUser()
    {
        $userId = $_GET['id'] ?? null;

        if ($userId) {
            $this->userModel->unbanUser((int)$userId);
            $_SESSION['flash_success'] = "Utilisateur débanni avec succès.";
        }

        header("Location: index.php?controller=Admin&action=listUsers");
        exit;
    }

}
