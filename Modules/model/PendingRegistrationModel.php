<?php

namespace SAE_CyberCigales_G5\Modules\model;

use SAE_CyberCigales_G5\includes\ConnectionDB;

class PendingRegistrationModel extends Database
{
    private ConnectionDB $db;

    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    public function __construct()
    {
        $this->db = ConnectionDB::getInstance();

        self::log("PendingRegistrationModel initialisé", "ok");
    }

    public function storePendingRegistration(
        string $nom,
        string $prenom,
        string $email,
        string $password
    ): bool {

        // supprimer une éventuelle ancienne inscription
        $this->db->delete('pending_registrations', ['email' => $email]);

        $this->db->insert('pending_registrations', [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $password,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        self::log("Inscription en attente enregistrée", "ok", [
            "email" => $email
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

        $result = $rows[0] ?? null;

        self::log("Recherche inscription en attente", "file", [
            "email" => $email,
            "found" => $result !== null
        ]);

        return $result;
    }

    public function getAllPendingRegistrations(): array
    {
        $rows = $this->db->getAll('pending_registrations');

        self::log("Liste des inscriptions en attente récupérée", "file", [
            "count" => count($rows)
        ]);

        return $rows;
    }

    public function getPendingRegistrationById(int $id): ?array
    {
        $rows = $this->db->getAll(
            'pending_registrations',
            ['id' => $id],
            1
        );

        $result = $rows[0] ?? null;

        self::log("Recherche inscription en attente par ID", "file", [
            "id" => $id,
            "found" => $result !== null
        ]);

        return $result;
    }

    public function deletePendingRegistrationById(int $id): bool
    {
        $deleted = $this->db->delete(
            'pending_registrations',
            ['id' => $id]
        ) > 0;

        self::log(
            "Suppression inscription en attente",
            $deleted ? "ok" : "warn",
            [
                "id" => $id
            ]
        );

        return $deleted;
    }
}
