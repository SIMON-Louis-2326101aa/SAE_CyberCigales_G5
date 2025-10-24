-- Script SQL pour créer les tables nécessaires au système de sélection d'équipe et mode solo
-- Projet: SAE CyberCigales G5 - L'Héritage Chiffré

-- Table pour les équipes (Alice ou Bob)
CREATE TABLE IF NOT EXISTS teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(50) NOT NULL COMMENT 'Alice ou Bob',
    session_id INT NOT NULL COMMENT 'Numéro de session (1, 2, 3, 4)',
    is_solo BOOLEAN DEFAULT FALSE COMMENT 'Mode solo ou groupe',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_team_session (team_name, session_id, is_solo),
    INDEX idx_session (session_id),
    INDEX idx_team_name (team_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour associer les joueurs aux équipes
CREATE TABLE IF NOT EXISTS team_members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Référence à users.id',
    team_id INT NOT NULL COMMENT 'Référence à teams.team_id',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_team (user_id, team_id),
    INDEX idx_user (user_id),
    INDEX idx_team (team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour suivre la progression des équipes dans le jeu
CREATE TABLE IF NOT EXISTS team_progress (
    progress_id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    acte INT NOT NULL COMMENT '1=Crypto, 2=Cyber, 3=Fusion',
    page_number INT NOT NULL COMMENT 'Numéro de la page/énigme',
    code_found VARCHAR(100) DEFAULT NULL COMMENT 'Code découvert',
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    UNIQUE KEY unique_team_acte_page (team_id, acte, page_number),
    INDEX idx_team_progress (team_id, acte),
    INDEX idx_completion (completed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour stocker les codes partiels d'Alice et Bob
CREATE TABLE IF NOT EXISTS team_codes (
    code_id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    acte INT NOT NULL COMMENT '1=Acte I, 2=Acte II',
    code_fragment VARCHAR(20) NOT NULL COMMENT 'Fragment de code (ex: CC----- ou --CC---)',
    obtained_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    UNIQUE KEY unique_team_acte_code (team_id, acte),
    INDEX idx_team_code (team_id, acte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les sessions de jeu (optionnel, pour statistiques)
CREATE TABLE IF NOT EXISTS game_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    session_name VARCHAR(100) NOT NULL COMMENT 'Nom de la session (ex: Session 18 Mars 2026 - 14h)',
    session_date DATE NOT NULL,
    session_time TIME NOT NULL,
    max_teams INT DEFAULT 4 COMMENT 'Nombre maximum d équipes',
    status ENUM('planned', 'active', 'completed', 'cancelled') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_date (session_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de données de test pour le développement
INSERT INTO game_sessions (session_name, session_date, session_time, max_teams, status) 
VALUES 
    ('Session Test - Développement', '2025-11-01', '14:00:00', 4, 'active'),
    ('Session 18 Mars 2026 - Matin', '2026-03-18', '09:00:00', 4, 'planned'),
    ('Session 18 Mars 2026 - Après-midi', '2026-03-18', '14:00:00', 4, 'planned');

-- Commentaires pour documentation
COMMENT ON TABLE teams IS 'Stocke les équipes créées (Alice ou Bob) avec leur session et mode (solo/groupe)';
COMMENT ON TABLE team_members IS 'Association entre utilisateurs et équipes';
COMMENT ON TABLE team_progress IS 'Suivi de la progression des équipes dans les énigmes';
COMMENT ON TABLE team_codes IS 'Fragments de code obtenus par chaque équipe à la fin de chaque acte';
COMMENT ON TABLE game_sessions IS 'Sessions de jeu planifiées ou en cours';

