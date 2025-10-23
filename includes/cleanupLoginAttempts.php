<?php
/**
 * Script de nettoyage des anciennes tentatives de connexion
 * À exécuter périodiquement (cron job recommandé)
 */

require_once __DIR__ . '/../Modules/model/loginAttemptModel.php';

try {
    $loginAttemptModel = new loginAttemptModel();
    $loginAttemptModel->cleanupOldAttempts();
    echo "Nettoyage des anciennes tentatives de connexion terminé avec succès.\n";
} catch (Exception $e) {
    echo "Erreur lors du nettoyage : " . $e->getMessage() . "\n";
}
