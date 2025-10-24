<?php
require_once __DIR__ . '/../model/teamModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

class teamController
{
    private teamModel $teamModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->teamModel = new teamModel();
    }

    /**
     * Afficher la page de sélection du mode de jeu
     */
    public function showModeSelection(): void
    {
        viewHandler::show('teamModeSelectionView', [
            'title' => 'Sélection du Mode de Jeu'
        ]);
    }

    /**
     * Afficher la page de sélection d'équipe
     */
    public function showTeamSelection(): void
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=redirection&action=openFormConnection');
            exit();
        }

        $mode = $_GET['mode'] ?? 'group';
        $isSolo = ($mode === 'solo');

        // Vérifier si l'utilisateur a déjà une équipe
        $currentTeam = $this->teamModel->getUserCurrentTeam($_SESSION['user_id']);

        viewHandler::show('teamSelectionView', [
            'title' => 'Sélection de l\'Équipe',
            'mode' => $mode,
            'is_solo' => $isSolo,
            'current_team' => $currentTeam
        ]);
    }

    /**
     * Créer ou rejoindre une équipe
     */
    public function joinTeam(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=team&action=showTeamSelection');
            exit();
        }

        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=redirection&action=openFormConnection');
            exit();
        }

        $teamName = $_POST['team_name'] ?? '';
        $sessionId = (int)($_POST['session_id'] ?? 1);
        $isSolo = isset($_POST['is_solo']) && $_POST['is_solo'] === '1';

        // Validation
        if (!in_array($teamName, ['Alice', 'Bob'])) {
            viewHandler::show('teamSelectionView', [
                'title' => 'Sélection de l\'Équipe',
                'error' => 'Nom d\'équipe invalide. Veuillez choisir Alice ou Bob.',
                'mode' => $isSolo ? 'solo' : 'group',
                'is_solo' => $isSolo
            ]);
            return;
        }

        // Créer ou récupérer l'équipe
        $teamId = $this->teamModel->createTeam($teamName, $sessionId, $isSolo);

        if (!$teamId) {
            viewHandler::show('teamSelectionView', [
                'title' => 'Sélection de l\'Équipe',
                'error' => 'Erreur lors de la création de l\'équipe. Veuillez réessayer.',
                'mode' => $isSolo ? 'solo' : 'group',
                'is_solo' => $isSolo
            ]);
            return;
        }

        // Ajouter l'utilisateur à l'équipe
        $success = $this->teamModel->addMemberToTeam($_SESSION['user_id'], $teamId);

        if ($success) {
            // Enregistrer l'équipe dans la session
            $_SESSION['team_id'] = $teamId;
            $_SESSION['team_name'] = $teamName;
            $_SESSION['is_solo'] = $isSolo;

            // Rediriger vers le jeu (Acte I)
            header('Location: index.php?controller=game&action=startActe&acte=1');
            exit();
        } else {
            viewHandler::show('teamSelectionView', [
                'title' => 'Sélection de l\'Équipe',
                'error' => 'Erreur lors de l\'ajout à l\'équipe. Veuillez réessayer.',
                'mode' => $isSolo ? 'solo' : 'group',
                'is_solo' => $isSolo
            ]);
        }
    }

    /**
     * Afficher les informations de l'équipe actuelle
     */
    public function showTeamInfo(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=redirection&action=openFormConnection');
            exit();
        }

        $currentTeam = $this->teamModel->getUserCurrentTeam($_SESSION['user_id']);

        if (!$currentTeam) {
            header('Location: index.php?controller=team&action=showTeamSelection');
            exit();
        }

        $members = $this->teamModel->getTeamMembers($currentTeam['team_id']);
        $stats = $this->teamModel->getTeamStats($currentTeam['team_id']);

        viewHandler::show('teamInfoView', [
            'title' => 'Informations de l\'Équipe',
            'team' => $currentTeam,
            'members' => $members,
            'stats' => $stats
        ]);
    }

    /**
     * Quitter l'équipe actuelle
     */
    public function leaveTeam(): void
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['team_id'])) {
            header('Location: index.php?controller=redirection&action=openHomepage');
            exit();
        }

        $success = $this->teamModel->removeMemberFromTeam($_SESSION['user_id'], $_SESSION['team_id']);

        if ($success) {
            // Nettoyer la session
            unset($_SESSION['team_id']);
            unset($_SESSION['team_name']);
            unset($_SESSION['is_solo']);

            header('Location: index.php?controller=team&action=showTeamSelection&success=Vous avez quitté l\'équipe.');
        } else {
            header('Location: index.php?controller=team&action=showTeamInfo&error=Erreur lors de la sortie de l\'équipe.');
        }
        exit();
    }

    /**
     * Enregistrer la progression (appelé depuis le contrôleur de jeu)
     */
    public function saveProgress(int $teamId, int $acte, int $pageNumber, ?string $codeFound = null): bool
    {
        return $this->teamModel->saveProgress($teamId, $acte, $pageNumber, $codeFound);
    }

    /**
     * Enregistrer un code d'équipe
     */
    public function saveTeamCode(int $teamId, int $acte, string $codeFragment): bool
    {
        return $this->teamModel->saveTeamCode($teamId, $acte, $codeFragment);
    }

    /**
     * Obtenir la progression d'une équipe
     */
    public function getProgress(int $teamId, ?int $acte = null): array
    {
        return $this->teamModel->getTeamProgress($teamId, $acte);
    }

    /**
     * Obtenir le code d'une équipe pour un acte
     */
    public function getTeamCode(int $teamId, int $acte): ?string
    {
        return $this->teamModel->getTeamCodeForActe($teamId, $acte);
    }

    /**
     * Vérifier si l'utilisateur a une équipe
     */
    public function hasTeam(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $team = $this->teamModel->getUserCurrentTeam($_SESSION['user_id']);
        return $team !== null;
    }

    /**
     * Obtenir l'équipe actuelle depuis la session
     */
    public function getCurrentTeam(): ?array
    {
        if (!isset($_SESSION['team_id'])) {
            return null;
        }

        return [
            'team_id' => $_SESSION['team_id'],
            'team_name' => $_SESSION['team_name'] ?? null,
            'is_solo' => $_SESSION['is_solo'] ?? false
        ];
    }
}

