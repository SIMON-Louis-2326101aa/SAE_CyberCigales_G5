<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../../includes/connectionDB.php';

class teamModel extends database
{
    private connectionDB $db;

    public function __construct()
    {
        $this->db = connectionDB::getInstance();
    }

    /**
     * Créer une nouvelle équipe
     * @param string $teamName 'Alice' ou 'Bob'
     * @param int $sessionId Numéro de session (1, 2, 3, 4)
     * @param bool $isSolo Mode solo ou non
     * @return int|false ID de l'équipe créée ou false en cas d'erreur
     */
    public function createTeam(string $teamName, int $sessionId, bool $isSolo = false): int|false
    {
        try {
            // Vérifier que le nom d'équipe est valide
            if (!in_array($teamName, ['Alice', 'Bob'])) {
                return false;
            }

            // Vérifier si l'équipe existe déjà
            $existingTeam = $this->getTeamByNameAndSession($teamName, $sessionId, $isSolo);
            if ($existingTeam) {
                return $existingTeam['team_id'];
            }

            $teamId = $this->db->insert('teams', [
                'team_name' => $teamName,
                'session_id' => $sessionId,
                'is_solo' => $isSolo ? 1 : 0
            ]);

            return $teamId;
        } catch (Exception $e) {
            error_log("Erreur lors de la création de l'équipe: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer une équipe par son nom et sa session
     */
    public function getTeamByNameAndSession(string $teamName, int $sessionId, bool $isSolo = false): ?array
    {
        $teams = $this->db->getAll('teams', [
            'team_name' => $teamName,
            'session_id' => $sessionId,
            'is_solo' => $isSolo ? 1 : 0
        ], 1);

        return $teams[0] ?? null;
    }

    /**
     * Ajouter un membre à une équipe
     */
    public function addMemberToTeam(int $userId, int $teamId): bool
    {
        try {
            // Vérifier si le membre est déjà dans une équipe pour cette session
            $existingMembership = $this->getUserCurrentTeam($userId);
            if ($existingMembership) {
                // Supprimer l'ancienne appartenance
                $this->db->delete('team_members', ['user_id' => $userId]);
            }

            $this->db->insert('team_members', [
                'user_id' => $userId,
                'team_id' => $teamId
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout du membre: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir l'équipe actuelle d'un utilisateur
     */
    public function getUserCurrentTeam(int $userId): ?array
    {
        $pdo = $this->db->getPdo();
        $sql = "SELECT t.*, tm.joined_at 
                FROM teams t
                INNER JOIN team_members tm ON t.team_id = tm.team_id
                WHERE tm.user_id = :user_id
                ORDER BY tm.joined_at DESC
                LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Obtenir tous les membres d'une équipe
     */
    public function getTeamMembers(int $teamId): array
    {
        $pdo = $this->db->getPdo();
        $sql = "SELECT u.id, u.nom, u.prenom, u.email, tm.joined_at
                FROM users u
                INNER JOIN team_members tm ON u.id = tm.user_id
                WHERE tm.team_id = :team_id
                ORDER BY tm.joined_at ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['team_id' => $teamId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Enregistrer la progression d'une équipe
     */
    public function saveProgress(int $teamId, int $acte, int $pageNumber, ?string $codeFound = null): bool
    {
        try {
            $pdo = $this->db->getPdo();
            
            // Vérifier si la progression existe déjà
            $existing = $this->db->getAll('team_progress', [
                'team_id' => $teamId,
                'acte' => $acte,
                'page_number' => $pageNumber
            ], 1);

            if ($existing) {
                // Mettre à jour
                return $this->db->update('team_progress', [
                    'code_found' => $codeFound,
                    'completed' => 1
                ], [
                    'team_id' => $teamId,
                    'acte' => $acte,
                    'page_number' => $pageNumber
                ]) > 0;
            } else {
                // Insérer
                $this->db->insert('team_progress', [
                    'team_id' => $teamId,
                    'acte' => $acte,
                    'page_number' => $pageNumber,
                    'code_found' => $codeFound,
                    'completed' => 1
                ]);
                return true;
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la sauvegarde de la progression: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir la progression d'une équipe
     */
    public function getTeamProgress(int $teamId, ?int $acte = null): array
    {
        $where = ['team_id' => $teamId];
        if ($acte !== null) {
            $where['acte'] = $acte;
        }

        return $this->db->getAll('team_progress', $where);
    }

    /**
     * Enregistrer un fragment de code pour une équipe
     */
    public function saveTeamCode(int $teamId, int $acte, string $codeFragment): bool
    {
        try {
            // Vérifier si un code existe déjà
            $existing = $this->db->getAll('team_codes', [
                'team_id' => $teamId,
                'acte' => $acte
            ], 1);

            if ($existing) {
                // Mettre à jour
                return $this->db->update('team_codes', [
                    'code_fragment' => $codeFragment
                ], [
                    'team_id' => $teamId,
                    'acte' => $acte
                ]) > 0;
            } else {
                // Insérer
                $this->db->insert('team_codes', [
                    'team_id' => $teamId,
                    'acte' => $acte,
                    'code_fragment' => $codeFragment
                ]);
                return true;
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la sauvegarde du code: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les codes d'une équipe
     */
    public function getTeamCodes(int $teamId): array
    {
        return $this->db->getAll('team_codes', ['team_id' => $teamId]);
    }

    /**
     * Obtenir le code d'une équipe pour un acte spécifique
     */
    public function getTeamCodeForActe(int $teamId, int $acte): ?string
    {
        $codes = $this->db->getAll('team_codes', [
            'team_id' => $teamId,
            'acte' => $acte
        ], 1);

        return $codes[0]['code_fragment'] ?? null;
    }

    /**
     * Obtenir toutes les équipes d'une session
     */
    public function getSessionTeams(int $sessionId): array
    {
        return $this->db->getAll('teams', ['session_id' => $sessionId]);
    }

    /**
     * Compter les membres d'une équipe
     */
    public function countTeamMembers(int $teamId): int
    {
        $pdo = $this->db->getPdo();
        $sql = "SELECT COUNT(*) as count FROM team_members WHERE team_id = :team_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['team_id' => $teamId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int)($result['count'] ?? 0);
    }

    /**
     * Supprimer un utilisateur d'une équipe
     */
    public function removeMemberFromTeam(int $userId, int $teamId): bool
    {
        try {
            return $this->db->delete('team_members', [
                'user_id' => $userId,
                'team_id' => $teamId
            ]) > 0;
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression du membre: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si un utilisateur est dans une équipe
     */
    public function isUserInTeam(int $userId, int $teamId): bool
    {
        $members = $this->db->getAll('team_members', [
            'user_id' => $userId,
            'team_id' => $teamId
        ], 1);

        return !empty($members);
    }

    /**
     * Obtenir les statistiques d'une équipe
     */
    public function getTeamStats(int $teamId): array
    {
        $pdo = $this->db->getPdo();
        
        // Nombre total de membres
        $memberCount = $this->countTeamMembers($teamId);
        
        // Progression par acte
        $sql = "SELECT acte, COUNT(*) as completed_pages
                FROM team_progress
                WHERE team_id = :team_id AND completed = 1
                GROUP BY acte";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['team_id' => $teamId]);
        $progress = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Codes obtenus
        $codes = $this->getTeamCodes($teamId);
        
        return [
            'member_count' => $memberCount,
            'progress_by_acte' => $progress,
            'codes' => $codes
        ];
    }
}

