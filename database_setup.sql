-- Script SQL pour créer la table login_attempts
-- Cette table stocke les tentatives de connexion échouées pour implémenter le rate limiting

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL, -- IPv6 peut faire jusqu'à 45 caractères
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_time (email, attempted_at),
    INDEX idx_ip_time (ip_address, attempted_at),
    INDEX idx_attempted_at (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour optimiser les requêtes de nettoyage et de vérification
CREATE INDEX idx_cleanup ON login_attempts (attempted_at);
