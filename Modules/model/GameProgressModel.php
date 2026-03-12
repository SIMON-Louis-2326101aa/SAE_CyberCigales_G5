<?php

namespace SAE_CyberCigales_G5\Modules\model;

use SAE_CyberCigales_G5\includes\ConnectionDB;

class GameProgressModel extends Database
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

        self::log('GameProgressModel initialisé', 'ok');
    }

    public function getByUserId(int $userId): ?array
    {
        $rows = $this->db->getAll('game_progress', ['user_id' => $userId], 1);
        $result = $rows[0] ?? null;

        self::log('Récupération progression utilisateur', 'file', [
            'user_id' => $userId,
            'found' => $result !== null,
        ]);

        return $result;
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

        self::log('Progression créée', 'ok', [
            'user_id' => $userId,
            'team' => $team,
            'level' => 1,
        ]);

        return true;
    }

    public function updateLevel(int $userId, int $level): bool
    {
        $stmt = $this->getBdd()->prepare("
            UPDATE game_progress
            SET level = GREATEST(level, :level)
            WHERE user_id = :user_id
        ");

        $success = $stmt->execute([
            'level' => $level,
            'user_id' => $userId
        ]);

        self::log('Mise à jour niveau progression', $success ? 'ok' : 'error', [
            'user_id' => $userId,
            'requested_level' => $level,
        ]);

        return $success;
    }

    public function getAllGameProgress(): array
    {
        $rows = $this->db->getAll('game_progress');

        self::log('Récupération de toutes les progressions', 'file', [
            'count' => count($rows),
        ]);

        return $rows;
    }

    public function startOrResumeGame(int $userId): void
    {
        $progress = $this->getByUserId($userId);

        if (!$progress) {
            self::log('StartOrResume ignoré: progression introuvable', 'warn', [
                'user_id' => $userId,
            ]);
            return;
        }

        if ($progress['status'] === 'finished') {
            self::log('StartOrResume ignoré: partie déjà terminée', 'info', [
                'user_id' => $userId,
            ]);
            return;
        }

        $data = [
            'last_start_time' => date('Y-m-d H:i:s'),
            'status' => 'in_progress'
        ];

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

        self::log('Partie démarrée ou reprise', 'ok', [
            'user_id' => $userId,
            'initialized_game_start' => isset($data['game_start_time']),
        ]);
    }

    public function pauseGame(int $userId): void
    {
        $progress = $this->getByUserId($userId);

        if (!$progress) {
            self::log('Pause ignorée: progression introuvable', 'warn', [
                'user_id' => $userId,
            ]);
            return;
        }

        if ($progress['status'] !== 'in_progress') {
            self::log('Pause ignorée: partie non en cours', 'info', [
                'user_id' => $userId,
                'status' => $progress['status'] ?? null,
            ]);
            return;
        }

        if (empty($progress['last_start_time'])) {
            self::log('Pause ignorée: last_start_time vide', 'warn', [
                'user_id' => $userId,
            ]);
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

        self::log('Partie mise en pause', 'ok', [
            'user_id' => $userId,
            'elapsed_added_sec' => $elapsed,
        ]);
    }

    public function updateTeam(int $userId, string $team): bool
    {
        $updated = $this->db->update(
            'game_progress',
            ['team' => $team],
            ['user_id' => $userId]
        ) > 0;

        self::log('Mise à jour équipe progression', $updated ? 'ok' : 'warn', [
            'user_id' => $userId,
            'team' => $team,
        ]);

        return $updated;
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

        self::log('Timer progression réinitialisé', 'ok', [
            'user_id' => $userId,
        ]);
    }

    public function deleteByUserId(int $userId): bool
    {
        $deleted = $this->db->delete('game_progress', ['user_id' => $userId]) > 0;

        self::log('Suppression progression utilisateur', $deleted ? 'ok' : 'warn', [
            'user_id' => $userId,
            'deleted' => $deleted,
        ]);

        return $deleted;
    }

    public function finishGame(int $userId): void
    {
        $progress = $this->getByUserId($userId);

        if (!$progress) {
            self::log('Finish ignoré: progression introuvable', 'warn', [
                'user_id' => $userId,
            ]);
            return;
        }

        if ($progress['status'] === 'finished') {
            self::log('Finish ignoré: partie déjà terminée', 'info', [
                'user_id' => $userId,
            ]);
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

        self::log('Partie terminée', 'ok', [
            'user_id' => $userId,
            'elapsed_added_sec' => $elapsed,
            'final_total_time_sec' => ($progress['total_time_sec'] ?? 0) + $elapsed,
        ]);
    }
}
