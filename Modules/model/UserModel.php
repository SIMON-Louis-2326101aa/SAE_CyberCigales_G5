<?php

namespace SAE_CyberCigales_G5\Modules\model;

use SAE_CyberCigales_G5\includes\ConnectionDB;

class UserModel extends Database
{
    private ConnectionDB $db;
    private PendingRegistrationModel $pendingModel;

    public function __construct()
    {
        $this->db = ConnectionDB::getInstance();
        $this->pendingModel = new PendingRegistrationModel();
    }

    // Créer le compte utilisateur après vérification
    public function createUserAfterVerification(string $email): bool
    {
        $pending = $this->pendingModel->getPendingRegistration($email);
        if (!$pending) {
            return false;
        }

        $success = $this->db->insert('users', [
                'nom' => $pending['nom'],
                'prenom' => $pending['prenom'],
                'email' => $pending['email'],
                'password' => $pending['password'],
                'email_verified' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]) > 0;

        if ($success) {
            $this->db->delete('pending_registrations', ['email' => $email]);
        }

        return $success;
    }

    public function findByEmail($email): bool
    {
        return $this->db->getElement('users', 'id', ['email' => $email]) !== null;
    }

    public function authenticate($email, $password)
    {
        $users = $this->db->getAll('users', ['email' => $email], 1);
        $user = $users[0] ?? null;

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email);
    }

    public function changePwd(string $newPassword, string $email): bool
    {
        return $this->db->update(
            'users',
            ['password' => password_hash($newPassword, PASSWORD_DEFAULT)],
            ['email' => $email]
        ) > 0;
    }

    public function delete(string $email): bool
    {
        return $this->db->delete('users', ['email' => $email]) > 0;
    }

    public function getEmailStatus($email): array
    {
        $inUsers = $this->db->getElement('users', 'id', ['email' => $email]) !== null;
        $inPending = $this->db->getElement('pending_registrations', 'id', ['email' => $email]) !== null;

        return [
            'exists' => $inUsers || $inPending,
            'verified' => $inUsers,
            'pending' => $inPending
        ];
    }

    public function getAllUsers(): array
    {
        return $this->db->getAll('users');
    }

    public function updateUser(int $id, string $nom, string $prenom, string $email): bool
    {
        return $this->db->update(
            'users',
            [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email
                ],
            ['id' => $id]
        ) > 0;
    }

    public function getUserById(int $id): ?array
    {
        $users = $this->db->getAll('users', ['id' => $id], 1);
        return $users[0] ?? null;
    }

    public function banUser(int $userId): bool
    {
        return $this->db->update(
            'users',
            ['is_banned' => 1],
            ['id' => $userId]
        ) > 0;
    }

    public function unbanUser(int $userId): bool
    {
        return $this->db->update(
            'users',
            ['is_banned' => 0],
            ['id' => $userId]
        ) > 0;
    }
}
