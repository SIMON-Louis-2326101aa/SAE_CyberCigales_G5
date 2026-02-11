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
            'level' => 1,
            'total_time_sec' => 0,
            'status' => 'paused'
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

    public function startOrResumeGame(int $userId): void
    {
        $progress = $this->getByUserId($userId);

        if (!$progress || $progress['status'] === 'finished') {
            return;
        }

        $data = [
            'last_start_time' => date('Y-m-d H:i:s'),
            'status' => 'in_progress'
        ];

        // Initialisation UNIQUEMENT si jamais démarré
        if (
            empty($progress['game_start_time']) ||
            $progress['game_start_time'] === '0000-00-00 00:00:00'
        ) {
            $data['game_start_time'] = date('Y-m-d H:i:s');
        }

        $this->db->update(
            'game_progress',
            $data,
            ['user_id' => $userId]
        );
    }

    public function pauseGame(int $userId): void
    {
        $progress = $this->getByUserId($userId);

        if (!$progress || $progress['status'] !== 'in_progress') {
            return;
        }

        if (empty($progress['last_start_time'])) {
            return;
        }

        $elapsed = time() - strtotime($progress['last_start_time']);

        $this->db->update(
            'game_progress',
            [
                'total_time_sec' => $progress['total_time_sec'] + $elapsed,
                'last_start_time' => null,
                'status' => 'paused'
            ],
            ['user_id' => $userId]
        );
    }

    public function updateTeam(int $userId, string $team): bool
    {
        return $this->db->update(
            'game_progress',
            ['team' => $team],
            ['user_id' => $userId]
        ) > 0;
    }

    public function resetTimer(int $userId): void
    {
        $this->db->update(
            'game_progress',
            [
                'total_time_sec' => 0,
                'last_start_time' => null,
                'game_start_time' => null,
                'game_end_time' => null,
                'status' => 'paused'
            ],
            ['user_id' => $userId]
        );
    }

    public function deleteByUserId(int $userId): bool
    {
        return $this->db->delete('game_progress', ['user_id' => $userId]) > 0;
    }

    public function finishGame(int $userId): void
    {
        $progress = $this->getByUserId($userId);

        if (!$progress || $progress['status'] === 'finished') {
            return;
        }

        $elapsed = 0;

        if (!empty($progress['last_start_time'])) {
            $elapsed = time() - strtotime($progress['last_start_time']);
        }

        $this->db->update(
            'game_progress',
            [
                'total_time_sec' => $progress['total_time_sec'] + $elapsed,
                'last_start_time' => null,
                'game_end_time' => date('Y-m-d H:i:s'),
                'status' => 'finished'
            ],
            ['user_id' => $userId]
        );
    }
}
