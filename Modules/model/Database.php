<?php

/**
 * Classe abstraite de base pour la gestion de la base de données
 *
 * Cette classe abstraite fournit les fonctionnalités de base pour la connexion
 * à la base de données. Elle utilise le pattern Singleton via connectionDB
 * pour s'assurer qu'une seule connexion est établie.
 *
 * Fonctionnalités :
 * - Gestion centralisée de la connexion PDO
 * - Pattern Singleton pour éviter les connexions multiples
 */

//require_once __DIR__ . '/../../includes/ConnectionDB.php';

namespace SAE_CyberCigales_G5\Modules\model;

use PDO;
use RuntimeException;
use SAE_CyberCigales_G5\includes\ConnectionDB;
use Throwable;

abstract class Database
{
    private static ?PDO $pdo = null;

    /** Initialise la connexion PDO unique via connectionDB */
    private static function setBdd(): void
    {
        try {
            $connexion = ConnectionDB::getInstance();
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
