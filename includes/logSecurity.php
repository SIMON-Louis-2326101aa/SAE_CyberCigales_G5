<?php
function logSecurityEvent($eventMessage) {
    // Fichier de log où les événements seront enregistrés
    $logFile = '/path/to/logs/security_events.log';

    // Message à enregistrer, avec date et heure
    $logMessage = '[' . date("Y-m-d H:i:s") . '] ' . $eventMessage . PHP_EOL;

    // Enregistrement du message dans le fichier de log
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>