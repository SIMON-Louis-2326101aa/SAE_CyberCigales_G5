<?php

/**
 * BackupDb.php
 * Script de sauvegarde SQL
 * - Charge les infos depuis .env
 * - Utilise un fichier temporaire --defaults-extra-file pour ne PAS exposer le mot de passe dans la ligne de commande
 * - Crée le répertoire de backup s'il n'existe pas
 * - Dump avec options sûres
 * - Rotation simple des sauvegardes
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Charge .env depuis la racine du projet
$rootDir = dirname(__DIR__);
require $rootDir . '/includes/functions.php';

$logDir = $rootDir . '/var/log';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0775, true);
}

$logFile = $logDir . '/app-' . date('Y-m-d') . '.log';
ini_set('log_errors', '1');
ini_set('error_log', $logFile);

registerLogRotation($logDir, $logFile);

if (function_exists('log_console')) {
    log_console('Backup DB: démarrage script', 'file', [
        'script' => basename(__FILE__),
    ]);
}

if (class_exists(\Dotenv\Dotenv::class)) {
    $dotenv = Dotenv\Dotenv::createImmutable($rootDir . '/config');
    $dotenv->load();

    if (function_exists('log_console')) {
        log_console('Backup DB: fichier .env chargé', 'info', [
            'env_path' => $rootDir . '/config/.env',
            'app_env' => $_ENV['APP_ENV'] ?? 'dev',
        ]);
    }
} else {
    if (function_exists('log_console')) {
        log_console('Backup DB: Dotenv indisponible', 'warn');
    }
}

// Récupère les paramètres DB
$dbHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '';
$dbName = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: '';
$dbUser = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: '';
$dbPass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

// Validation minimale
if ($dbHost === '' || $dbName === '' || $dbUser === '') {
    if (function_exists('log_console')) {
        log_console('Backup DB: variables DB manquantes pour le backup', 'error', [
            'has_host' => $dbHost !== '',
            'has_name' => $dbName !== '',
            'has_user' => $dbUser !== '',
        ]);
    }
    exit(1);
}

// Répertoire de backup
$backupDir = '/home/escapethecode/backups';
if (!is_dir($backupDir)) {
    if (!@mkdir($backupDir, 0755, true) && !is_dir($backupDir)) {
        if (function_exists('log_console')) {
            log_console('Backup DB: impossible de créer le répertoire de backup', 'error', [
                'backup_dir' => $backupDir,
            ]);
        }
        exit(1);
    }

    if (function_exists('log_console')) {
        log_console('Backup DB: répertoire de backup créé', 'ok', [
            'backup_dir' => $backupDir,
        ]);
    }
}

// Nom de fichier
$fileName = $dbName . '_' . date('Ymd_His') . '.sql';
$fullPath = rtrim($backupDir, "/\\") . DIRECTORY_SEPARATOR . $fileName;

// Crée un fichier temporaire de config pour éviter d’exposer le mot de passe
$defaultsFile = tempnam(sys_get_temp_dir(), 'mysqldump_');
if ($defaultsFile === false) {
    if (function_exists('log_console')) {
        log_console('Backup DB: impossible de créer le fichier temporaire des credentials', 'error');
    }
    exit(1);
}

if (function_exists('log_console')) {
    log_console('Backup DB: fichier temporaire credentials créé', 'file', [
        'temp_file' => $defaultsFile,
    ]);
}

// Écrit les infos dans le fichier .cnf temporaire
$defaultsContent = "[client]\nuser={$dbUser}\npassword={$dbPass}\nhost={$dbHost}\n";
file_put_contents($defaultsFile, $defaultsContent);
@chmod($defaultsFile, 0600);

// Construit la commande mysqldump
$command = sprintf(
    implode(' ', [
        'mysqldump',
        '--defaults-extra-file=' . escapeshellarg($defaultsFile),
        '--single-transaction',
        '--routines',
        '--triggers',
        '--events',
        '--default-character-set=utf8mb4',
        escapeshellarg($dbName),
        '> ' . escapeshellarg($fullPath),
        '2>&1'
    ])
);

if (function_exists('log_console')) {
    log_console('Backup DB: lancement du dump MySQL', 'file', [
        'db_name' => $dbName,
        'backup_file' => $fullPath,
        'backup_dir' => $backupDir,
    ]);
}

// Exécute la commande et capture la sortie
$output = shell_exec($command);

// Nettoie le fichier temporaire des infos au plus tôt
@unlink($defaultsFile);

if (function_exists('log_console')) {
    log_console('Backup DB: fichier temporaire credentials supprimé', 'file');
}

// Vérifie le résultat
if (!is_file($fullPath) || filesize($fullPath) === 0) {
    if (function_exists('log_console')) {
        log_console('Backup DB: échec du dump MySQL (fichier vide ou absent)', 'error', [
            'backup_file' => $fullPath,
            'shell_output' => is_string($output) ? mb_substr($output, 0, 500) : null,
        ]);
    }
    exit(1);
}

if (function_exists('log_console')) {
    log_console('Backup DB: dump terminé', 'ok', [
        'backup_file' => $fullPath,
        'size_bytes' => filesize($fullPath),
    ]);
}

// --- Rotation des sauvegardes ---
$maxBackupsToKeep = 5;

// Liste les .sql et trie par nom
$files = glob(rtrim($backupDir, "/\\") . DIRECTORY_SEPARATOR . '*.sql') ?: [];
natsort($files);
$files = array_values($files);

if (function_exists('log_console')) {
    log_console('Backup DB: analyse rotation sauvegardes', 'info', [
        'total_backups_found' => count($files),
        'max_backups_to_keep' => $maxBackupsToKeep,
    ]);
}

$toDelete = count($files) - $maxBackupsToKeep;

if ($toDelete > 0) {
    for ($i = 0; $i < $toDelete; $i++) {
        $old = $files[$i];

        if (@unlink($old)) {
            if (function_exists('log_console')) {
                log_console('Backup DB: ancienne sauvegarde supprimée', 'file', [
                    'file' => $old,
                ]);
            }
        } else {
            if (function_exists('log_console')) {
                log_console('Backup DB: suppression sauvegarde impossible', 'warn', [
                    'file' => $old,
                ]);
            }
        }
    }
}

if (function_exists('log_console')) {
    log_console('Backup DB: rotation des sauvegardes terminée', 'ok', [
        'remaining_backups' => min(count($files), $maxBackupsToKeep),
    ]);
}

exit(0);