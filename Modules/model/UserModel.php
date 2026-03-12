<?php

namespace SAE_CyberCigales_G5\Modules\model;

use SAE_CyberCigales_G5\includes\ConnectionDB;

class UserModel extends Database
{
    private ConnectionDB $db;
    private PendingRegistrationModel $pendingModel;

    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    /**
     * Constructeur du UserModel
     *
     * Permet l'injection de dépendances pour les tests avec mocks.
     * Si aucune dépendance n'est fournie, utilise les instances par défaut.
     *
     * @param ConnectionDB|null $db Connexion à la base de données (ou mock pour les tests)
     * @param PendingRegistrationModel|null $pendingModel Modèle des inscriptions en attente (ou mock pour les tests)
     */
    public function __construct(?ConnectionDB $db = null, ?PendingRegistrationModel $pendingModel = null)
    {
        $this->db = $db ?? ConnectionDB::getInstance();
        $this->pendingModel = $pendingModel ?? new PendingRegistrationModel();

        self::log('UserModel initialisé', 'ok');
    }

    // Créer le compte utilisateur après vérification
    public function createUserAfterVerification(string $email): bool
    {
        $pending = $this->pendingModel->getPendingRegistration($email);

        if (!$pending) {
            self::log(
                'Création utilisateur après vérification impossible: inscription en attente introuvable',
                'warn',
                [
                    'email' => $email,
                ]
            );
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

            self::log('Utilisateur créé après vérification email', 'ok', [
                'email' => $email,
            ]);
        } else {
            self::log('Échec création utilisateur après vérification email', 'error', [
                'email' => $email,
            ]);
        }

        return $success;
    }

    public function findByEmail($email): bool
    {
        $exists = $this->db->getElement('users', 'id', ['email' => $email]) !== null;

        self::log('Recherche utilisateur par email', 'file', [
            'email' => $email,
            'found' => $exists,
        ]);

        return $exists;
    }

    public function authenticate($email, $password)
    {
        $users = $this->db->getAll('users', ['email' => $email], 1);
        $user = $users[0] ?? null;

        if ($user && password_verify($password, $user['password'])) {
            self::log('Authentification utilisateur réussie dans UserModel', 'ok', [
                'email' => $email,
                'user_id' => $user['id'] ?? null,
            ]);
            return $user;
        }

        self::log('Authentification utilisateur échouée dans UserModel', 'info', [
            'email' => $email,
        ]);

        return null;
    }

    public function emailExists(string $email): bool
    {
        $exists = $this->findByEmail($email);

        self::log('Vérification existence email', 'file', [
            'email' => $email,
            'exists' => $exists,
        ]);

        return $exists;
    }

    public function changePwd(string $newPassword, string $email): bool
    {
        $updated = $this->db->update(
            'users',
            ['password' => password_hash($newPassword, PASSWORD_DEFAULT)],
            ['email' => $email]
        ) > 0;

        self::log('Changement mot de passe utilisateur', $updated ? 'ok' : 'error', [
            'email' => $email,
        ]);

        return $updated;
    }

    public function delete(string $email): bool
    {
        $deleted = $this->db->delete('users', ['email' => $email]) > 0;

        self::log('Suppression utilisateur', $deleted ? 'ok' : 'warn', [
            'email' => $email,
        ]);

        return $deleted;
    }

    public function getEmailStatus($email): array
    {
        $inUsers = $this->db->getElement('users', 'id', ['email' => $email]) !== null;
        $inPending = $this->db->getElement('pending_registrations', 'id', ['email' => $email]) !== null;

        $result = [
            'exists' => $inUsers || $inPending,
            'verified' => $inUsers,
            'pending' => $inPending
        ];

        self::log('Statut email récupéré', 'file', [
            'email' => $email,
            'exists' => $result['exists'],
            'verified' => $result['verified'],
            'pending' => $result['pending'],
        ]);

        return $result;
    }

    public function getAllUsers(): array
    {
        $rows = $this->db->getAll('users');

        self::log('Liste complète des utilisateurs récupérée', 'file', [
            'count' => count($rows),
        ]);

        return $rows;
    }

    public function updateUser(int $id, string $nom, string $prenom, string $email): bool
    {
        $updated = $this->db->update(
            'users',
            [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email
                ],
            ['id' => $id]
        ) > 0;

        self::log('Mise à jour utilisateur', $updated ? 'ok' : 'warn', [
            'user_id' => $id,
            'email' => $email,
        ]);

        return $updated;
    }

    public function getUserById(int $id): ?array
    {
        $users = $this->db->getAll('users', ['id' => $id], 1);
        $result = $users[0] ?? null;

        self::log('Recherche utilisateur par ID', 'file', [
            'user_id' => $id,
            'found' => $result !== null,
        ]);

        return $result;
    }

    public function banUser(int $userId, string $reason): bool
    {
        $updated = $this->db->update(
            'users',
            [
                    'is_banned' => 1,
                    'ban_reason' => $reason,
                ],
            ['id' => $userId]
        ) > 0;

        self::log('Utilisateur banni', $updated ? 'ok' : 'error', [
            'user_id' => $userId,
            'has_reason' => trim($reason) !== '',
        ]);

        return $updated;
    }

    public function unbanUser(int $userId): bool
    {
        $updated = $this->db->update(
            'users',
            ['is_banned' => 0],
            ['id' => $userId]
        ) > 0;

        self::log('Utilisateur débanni', $updated ? 'ok' : 'error', [
            'user_id' => $userId,
        ]);

        return $updated;
    }

    public function incrementNbTry(int $userId): int
    {
        $user = $this->getUserById($userId);
        $current = (int)($user['nbTry'] ?? 0);
        $newValue = $current + 1;

        $this->db->update(
            'users',
            ['nbTry' => $newValue],
            ['id' => $userId]
        );

        self::log('Incrémentation nbTry utilisateur', 'ok', [
            'user_id' => $userId,
            'old_value' => $current,
            'new_value' => $newValue,
        ]);

        return $newValue;
    }
}
