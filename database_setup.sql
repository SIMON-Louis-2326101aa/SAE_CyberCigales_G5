-- Script de création de la base de données pour SAE CyberCigales
-- À exécuter dans MySQL pour créer la structure nécessaire

CREATE DATABASE IF NOT EXISTS cybercigales_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE cybercigales_db;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Index pour optimiser les recherches par email
CREATE INDEX idx_email ON users(email);

-- Insertion d'un utilisateur de test (optionnel)
-- Mot de passe : Test123! (haché avec BCRYPT)
INSERT INTO users (nom, prenom, email, password) VALUES 
('Test', 'User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE email=email;
