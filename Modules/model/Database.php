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

namespace SAE_CyberCigales_G5\Modules\model;

use PDO;
use SAE_CyberCigales_G5\includes\ConnectionDB;

abstract class Database
{
    private static function log(string $message, string $type): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type);
        }
    }
    protected function getBdd(): PDO
    {
        $connection = ConnectionDB::getInstance();
        return $connection->getPdo();
    }
}
