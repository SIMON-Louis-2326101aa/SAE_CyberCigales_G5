<?php

/**
 * Mailer.php
 * Utilitaire simple pour l'envoi d'emails.
 * Utilise la fonction mail() de PHP avec des en-têtes basiques.
 * Note: Pour une application en production, envisager une bibliothèque plus robuste.
 */

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

final class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        // Configuration des en-têtes email
        $headers = [];
        $headers[] = 'From: no-reply@escapethecode.alwaysdata.net';
        $headers[] = 'Reply-To: no-reply@escapethecode.alwaysdata.net';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $result = @mail($to, $subject, $body, implode("\r\n", $headers));

        // Log pour débogage (visible dans les logs AlwaysData)
        if (!$result) {
            error_log("Échec d'envoi d'email vers $to");
        }

        return $result;
    }
}
