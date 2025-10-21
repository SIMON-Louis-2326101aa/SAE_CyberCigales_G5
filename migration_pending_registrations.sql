-- ============================================
-- Migration: Ajout table pending_registrations
-- Date: 2025-10-19
-- Description: Table pour stocker les inscriptions en attente de vérification email
-- ============================================

-- Créer la table pending_registrations
CREATE TABLE IF NOT EXISTS `pending_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ajouter le champ email_verified à la table users si il n'existe pas
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `email_verified` TINYINT(1) DEFAULT FALSE;

-- Message de confirmation
SELECT 'Migration terminée avec succès!' AS message;

