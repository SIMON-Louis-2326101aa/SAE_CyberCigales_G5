<?php

namespace SAE_CyberCigales_G5\Modules\model;

use SAE_CyberCigales_G5\includes\ConnectionDB;

class PendingRegistrationModel extends Database
{
    private ConnectionDB $db;

    public function __construct()
    {
        $this->db = ConnectionDB::getInstance();
    }

    public function storePendingRegistration(
        string $nom,
        string $prenom,
        string $email,
        string $password
    ): bool {
        $this->db->delete('pending_registrations', ['email' => $email]);

        $this->db->insert('pending_registrations', [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $password,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    public function getPendingRegistration(string $email): ?array
    {
        $rows = $this->db->getAll(
            'pending_registrations',
            ['email' => $email],
            1
        );

        return $rows[0] ?? null;
    }

    public function getAllPendingRegistrations(): array
    {
        return $this->db->getAll('pending_registrations');
    }

    public function getPendingRegistrationById(int $id): ?array
    {
        $rows = $this->db->getAll(
            'pending_registrations',
            ['id' => $id],
            1
        );

        return $rows[0] ?? null;
    }

    public function deletePendingRegistrationById(int $id): bool
    {
        return $this->db->delete(
            'pending_registrations',
            ['id' => $id]
        ) > 0;
    }
}
