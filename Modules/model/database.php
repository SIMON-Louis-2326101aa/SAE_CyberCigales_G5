<?php
require_once __DIR__ . '/../../includes/connectionDB.php';

abstract class database
{
    private static ?PDO $pdo = null;

    /** Initialise la connexion PDO unique via connectionDB */
    private static function setBdd(): void
    {
        try {
            $connexion = connectionDB::getInstance();
            self::$pdo = $connexion->getPdo();

            if (function_exists('log_console')) {
                log_console('Connexion PDO initialisée avec succès', 'ok'); // ✅
            }
        } catch (Throwable $e) {
            if (function_exists('log_console')) {
                log_console('Erreur lors de l’initialisation de PDO : ' . $e->getMessage(), 'error'); // ❌
            }
            throw new RuntimeException('Impossible d’établir la connexion PDO.');
        }
    }

    /** Retourne l’unique instance PDO */
    protected function getBdd(): PDO
    {
        if (self::$pdo === null) {
            self::setBdd();
        }

        if (function_exists('log_console')) {
            log_console('Connexion PDO récupérée depuis Database', 'file'); // 📄
        }

        return self::$pdo;
    }
}
