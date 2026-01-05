<?php
namespace SAE_CyberCigales_G5\Modules\model;

use SAE_CyberCigales_G5\includes\ConnectionDB;

class GameProgressModel extends Database
{
    private ConnectionDB $db;

    public function __construct()
    {
        $this->db = ConnectionDB::getInstance();
    }

    public function getByUserId(int $userId): ?array
    {
        $rows = $this->db->getAll('game_progress', ['user_id' => $userId], 1);
        return $rows[0] ?? null;
    }

    public function create(int $userId, string $team): bool
    {
        $this->db->insert('game_progress', [
            'user_id' => $userId,
            'team' => $team,
            'level' => 1
        ]);
        return true;
    }

    public function updateLevel(int $userId, int $level): bool
    {
        return $this->db->update(
            'game_progress',
            ['level' => $level],
            ['user_id' => $userId]
        ) > 0;
    }

    public function getAllGameProgress(): array
    {
        return $this->db->getAll('game_progress');
    }
}
