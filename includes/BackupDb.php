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

$logDir  = $rootDir . '/var/log';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0775, true);
}

$logFile = $logDir . '/app-' . date('Y-m-d') . '.log';
ini_set('log_errors', '1');
ini_set('error_log', $logFile);

// programme la rotation à la fin du script
registerLogRotation($logDir, $logFile);

// (Optionnel) tu peux log ici que le backup démarre
if (function_exists('log_console')) {
    log_console('Backup DB: démarrage', 'file');
}
if (class_exists(Dotenv\Dotenv::class)) {
    Dotenv\Dotenv::createImmutable($rootDir)->load();
    if (function_exists('log_console')) {
        log_console('Backup DB: fichier .env chargé', 'info');
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
        log_console('Backup DB: variables DB manquantes pour le backup', 'error');
        log_console('Backup DB: variables manquantes (DB_HOST/DB_NAME/DB_USER)', 'error');
    }
    exit(1);
}

// Répertoire de backup.
$backupDir = '/home/escapethecode/backups';
if (!is_dir($backupDir)) {
    if (!@mkdir($backupDir, 0755, true) && !is_dir($backupDir)) {
        if (function_exists('log_console')) {
            log_console("Backup DB: impossible de créer le répertoire: {$backupDir}", 'error');
        }
        exit(1);
    }
}

// Nom de fichier : tri naturel fiable => YYYYMMDD_HHMMSS
$fileName = $dbName . '_' . date('Ymd_His') . '.sql';
$fullPath = rtrim($backupDir, "/\\") . DIRECTORY_SEPARATOR . $fileName;

// Crée un fichier temporaire de config pour éviter d’exposer le mot de passe
$defaultsFile = tempnam(sys_get_temp_dir(), 'mysqldump_');
if ($defaultsFile === false) {
    if (function_exists('log_console')) {
        log_console('Backup DB: impossible de créer le fichier temporaire pour credentials', 'error');
    }
    exit(1);
}

// Écrit les infos dans le fichier .cnf temporaire
// ATTENTION : on évite de logguer ce contenu !
$defaultsContent = "[client]\nuser={$dbUser}\npassword={$dbPass}\nhost={$dbHost}\n";
file_put_contents($defaultsFile, $defaultsContent);
@chmod($defaultsFile, 0600);

// Construis la commande mysqldump
$command = sprintf(
    'mysqldump --defaults-extra-file=%s --single-transaction --routines --triggers
     --events --set-gtid-purged=OFF --default-character-set=utf8mb4 %s > %s 2>&1',
    escapeshellarg($defaultsFile),
    escapeshellarg($dbName),
    escapeshellarg($fullPath)
);

// Exécute la commande et capture la sortie
if (function_exists('log_console')) {
    log_console('Backup DB: lancement du dump MySQL', 'file');
}
$output = shell_exec($command);

// Nettoie le fichier temporaire des infos au plus tôt
@unlink($defaultsFile);

// Vérifie le résultat
if (!is_file($fullPath) || filesize($fullPath) === 0) {
    if (function_exists('log_console')) {
        log_console('Backup DB: échec du dump MySQL (fichier vide ou absent)', 'error');
    }
    exit(1);
}

if (function_exists('log_console')) {
    log_console("Backup DB: dump terminé: {$fullPath}", 'ok');
}

// --- Rotation des sauvegardes ---
// Conserver au max 5 sauvegardes les plus récentes
$maxBackupsToKeep = 5;

// Liste les .sql et trie par nom
$files = glob(rtrim($backupDir, "/\\") . DIRECTORY_SEPARATOR . '*.sql') ?: [];
natsort($files);                 // du plus ancien au plus récent
$files = array_values($files);   // réindexe

$toDelete = count($files) - $maxBackupsToKeep;
if ($toDelete > 0) {
    for ($i = 0; $i < $toDelete; $i++) {
        $old = $files[$i];
        if (@unlink($old)) {
            if (function_exists('log_console')) {
                log_console("Backup DB: ancienne sauvegarde supprimée", 'file', ['file' => $old]);
            }
        } else {
            if (function_exists('log_console')) {
                log_console("Backup DB: suppression impossible", 'warn', ['file' => $old]);
            }
        }
    }
}

if (function_exists('log_console')) {
    log_console('Backup DB: rotation des sauvegardes terminée', 'ok');
}

exit(0);
