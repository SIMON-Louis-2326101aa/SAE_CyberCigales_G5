<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\Mailer;
use SAE_CyberCigales_G5\includes\ViewHandler;
use SAE_CyberCigales_G5\Modules\model\GameProgressModel;
use SAE_CyberCigales_G5\Modules\model\PendingRegistrationModel;
use SAE_CyberCigales_G5\Modules\model\UserModel;

class AdminController
{
    private UserModel $userModel;
    private PendingRegistrationModel $pendingRegistrationModel;
    private GameProgressModel $gameProgressModel;

    private function logAdmin(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->pendingRegistrationModel = new PendingRegistrationModel();
        $this->gameProgressModel = new GameProgressModel();

        $this->logAdmin('AdminController initialisé', 'ok', [
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);

        // Si non Admin, pas accès aux pages admin
        if (!isset($_SESSION['email']) || $_SESSION['email'] !== $_ENV['ADMIN_EMAIL']) {
            $this->logAdmin('Accès admin refusé', 'warn', [
                'user_id' => $_SESSION['user_id'] ?? null,
                'uri' => $_SERVER['REQUEST_URI'] ?? null,
            ]);

            header("Location: index.php?controller=Redirection&action=openHomepage");
            exit;
        }

        $this->logAdmin('Accès admin autorisé', 'info', [
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);
    }

    // Liste les utilisateurs grâce au userListView
    public function listUsers()
    {
        $users = $this->userModel->getAllUsers();
        $pendingUsers = $this->pendingRegistrationModel->getAllPendingRegistrations();
        $progressGames = $this->gameProgressModel->getAllGameProgress();

        $this->logAdmin('Liste admin chargée', 'file', [
            'users_count' => count($users),
            'pending_users_count' => count($pendingUsers),
            'progress_games_count' => count($progressGames),
        ]);

        ViewHandler::show('admin/userListView', [
            'users' => $users,
            'pendingUsers' => $pendingUsers,
            'progressGames' => $progressGames
        ]);
    }

    // Permet de modifier les informations liées aux utilisateurs
    public function editUser()
    {
        $userId = $_GET['id'] ?? null;

        if ($userId == 5) {
            $this->logAdmin('Tentative modification compte admin bloquée', 'warn', [
                'target_user_id' => $userId,
            ]);

            $_SESSION['flash_error'] = 'Le compte administrateur ne peut pas être modifié.';
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        if (!$userId) {
            $this->logAdmin('Edition utilisateur impossible: ID manquant', 'warn');
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';

            $this->logAdmin('Modification utilisateur demandée', 'info', [
                'target_user_id' => (int)$userId,
                'new_email' => $email,
            ]);

            $this->userModel->updateUser((int)$userId, $nom, $prenom, $email);

            $_SESSION['flash_success'] = "L'utilisateur a été modifié avec succès.";

            $this->logAdmin('Modification utilisateur réussie', 'ok', [
                'target_user_id' => (int)$userId,
            ]);

            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        $user = $this->userModel->getUserById((int)$userId);

        if (!$user) {
            $this->logAdmin('Edition utilisateur impossible: utilisateur introuvable', 'warn', [
                'target_user_id' => (int)$userId,
            ]);

            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        $this->logAdmin('Affichage formulaire édition utilisateur', 'file', [
            'target_user_id' => (int)$userId,
        ]);

        ViewHandler::show('admin/userEditView', ['user' => $user]);
    }

    public function deleteUser()
    {
        $userId = $_GET['id'] ?? null;

        if ($userId == 5) {
            $this->logAdmin('Tentative suppression compte admin bloquée', 'warn', [
                'target_user_id' => $userId,
            ]);

            $_SESSION['flash_error'] = 'Le compte administrateur ne peut pas être supprimé.';
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        if ($userId) {
            $user = $this->userModel->getUserById((int)$userId);

            if ($user) {
                $this->userModel->delete($user['email']);

                $_SESSION['flash_success'] = "L'utilisateur a été supprimé avec succès.";

                $this->logAdmin('Suppression utilisateur réussie', 'ok', [
                    'target_user_id' => (int)$userId,
                    'email' => $user['email'] ?? null,
                ]);
            } else {
                $this->logAdmin('Suppression utilisateur: cible introuvable', 'warn', [
                    'target_user_id' => (int)$userId,
                ]);
            }
        } else {
            $this->logAdmin('Suppression utilisateur impossible: ID manquant', 'warn');
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

                $this->logAdmin('Inscription en attente approuvée', 'ok', [
                    'pending_id' => (int)$pendingId,
                    'email' => $pendingUser['email'] ?? null,
                ]);
            } else {
                $this->logAdmin('Approbation inscription impossible: entrée introuvable', 'warn', [
                    'pending_id' => (int)$pendingId,
                ]);
            }
        } else {
            $this->logAdmin('Approbation inscription impossible: ID manquant', 'warn');
        }

        header("Location: index.php?controller=Admin&action=listUsers");
        exit;
    }

    public function deleteRegistration()
    {
        $pendingId = $_GET['id'] ?? null;

        if ($pendingId) {
            $this->pendingRegistrationModel->deletePendingRegistrationById((int)$pendingId);

            $this->logAdmin('Inscription en attente supprimée', 'ok', [
                'pending_id' => (int)$pendingId,
            ]);
        } else {
            $this->logAdmin('Suppression inscription en attente impossible: ID manquant', 'warn');
        }

        header("Location: index.php?controller=Admin&action=listUsers");
        exit;
    }

    public function banUser()
    {
        $userId = $_GET['id'] ?? null;
        $reason = $_POST['reason'] ?? '';
        $email = $_POST['email'] ?? '';

        if ($userId == 5) {
            $this->logAdmin('Tentative bannissement compte admin bloquée', 'warn', [
                'target_user_id' => $userId,
            ]);

            $_SESSION['flash_error'] = "Le compte administrateur ne peut pas être banni.";
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        if ($userId) {
            $this->userModel->banUser((int)$userId, $reason);

            $subject = 'Votre compte a etait banni.';
            $message = $this->renderEmailTemplate([
                'ban_reason' => $reason
            ]);

            Mailer::send($email, $subject, $message);

            $_SESSION['flash_success'] = "Utilisateur banni avec succès.";

            $this->logAdmin('Utilisateur banni', 'ok', [
                'target_user_id' => (int)$userId,
                'email' => $email,
                'has_reason' => trim($reason) !== '',
            ]);
        } else {
            $this->logAdmin('Bannissement impossible: ID manquant', 'warn');
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

            $this->logAdmin('Utilisateur débanni', 'ok', [
                'target_user_id' => (int)$userId,
            ]);
        } else {
            $this->logAdmin('Débannissement impossible: ID manquant', 'warn');
        }

        header("Location: index.php?controller=Admin&action=listUsers");
        exit;
    }

    public function showBanForm()
    {
        $userId = $_GET['id'] ?? null;

        if (!$userId) {
            $this->logAdmin('Affichage formulaire bannissement impossible: ID manquant', 'warn');
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        $user = $this->userModel->getUserById((int)$userId);

        if (!$user) {
            $this->logAdmin('Affichage formulaire bannissement impossible: utilisateur introuvable', 'warn', [
                'target_user_id' => (int)$userId,
            ]);
            header("Location: index.php?controller=Admin&action=listUsers");
            exit;
        }

        $this->logAdmin('Affichage formulaire bannissement', 'file', [
            'target_user_id' => (int)$userId,
        ]);

        ViewHandler::show('admin/userBanView', ['user' => $user]);
    }

    private function renderEmailTemplate(array $data): string
    {
        extract($data);

        ob_start();
        require __DIR__ . '/../view/email/banniEmail.php';
        return (string)ob_get_clean();
    }
}
