<?php
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Définir les paramètres (lus depuis le .env)
$DB_HOST = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
$DB_NAME = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
$DB_USER = $_ENV['DB_USER'] ?? getenv('DB_USER');
$DB_PASS = $_ENV['DB_PASS'] ?? getenv('DB_PASS');

// Définir le chemin de sauvegarde et le nom du fichier
$backupDir = '/home/escapethecode/backups/';
$fileName = $DB_NAME . '_' . date('dmY_His') . '.sql';
$fullPath = $backupDir . $fileName;

// Construire la commande mysqldump (Utilisation de shell_exec est nécessaire pour exécuter des commandes système)
// La commande utilise les identifiants pour dumper la DB
$command = sprintf(
    'mysqldump --opt -h%s -u%s -p%s %s > %s',
    escapeshellarg($DB_HOST),
    escapeshellarg($DB_USER),
    escapeshellarg($DB_PASS),
    escapeshellarg($DB_NAME),
    escapeshellarg($fullPath)
);

//  Exécuter la commande
$output = shell_exec($command . ' 2>&1'); // 2>&1 capture les erreurs

// Gestion des erreurs (Optionnel: log ou alerte)
if ($output) {
    // Si $output contient quelque chose, il y a probablement eu une erreur (ex: mauvaise connexion DB, chemin invalide)
    error_log("Erreur lors de la sauvegarde de la DB: " . $output);
    // Dans un vrai système, vous enverriez un email d'alerte ici.
} else {
    // --- Logique de Nettoyage ---
    // Définir le chemin et le nombre max de sauvegardes à conserver (ex: 5 semaines)
    $backupDir = '/home/escapethecode/backups/';
    $maxBackupsToKeep = 5;

    // Lire tous les fichiers SQL dans le répertoire
    $files = glob($backupDir . '*.sql');

    //  Triez les fichiers par date/nom (le plus récent en dernier)
    usort($files, 'strnatcmp'); // Tri par nom (si le nom contient la date/heure, c'est suffisant)

    // 3. Compter le nombre de fichiers à supprimer
    $count = count($files);
    $numToDelete = $count - $maxBackupsToKeep;

    if ($numToDelete > 0) {
        // 4. Parcourir et supprimer les plus anciens
        for ($i = 0; $i < $numToDelete; $i++) {
            if (unlink($files[$i])) {
                // Fichier supprimé avec succès
            } else {
                error_log("Impossible de supprimer le vieux fichier de sauvegarde: " . $files[$i]);
            }
        }
    }
}

// Pour des raisons de sécurité, le script ne doit rien afficher en sortie
exit(0);