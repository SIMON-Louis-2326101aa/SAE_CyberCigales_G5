<?php

/**
 * Mailer.php
 * Utilitaire simple pour l'envoi d'emails.
 * Utilise la fonction mail() de PHP avec des en-têtes basiques.
 *
 * Note :
 * Pour une application en production, envisager une bibliothèque plus robuste
 * comme PHPMailer ou Symfony Mailer.
 */

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

final class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        // Validation minimale de l'email
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            if (function_exists('log_console')) {
                log_console('Mailer: email invalide', 'warn', [
                    'to' => $to
                ]);
            }

            return false;
        }

        // Configuration des en-têtes email
        $headers = [];
        $headers[] = 'From: no-reply@escapethecode.alwaysdata.net';
        $headers[] = 'Reply-To: no-reply@escapethecode.alwaysdata.net';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        if (function_exists('log_console')) {
            log_console('Mailer: tentative envoi email', 'file', [
                'to' => $to,
                'subject_length' => strlen($subject)
            ]);
        }

        $result = @mail($to, $subject, $body, implode("\r\n", $headers));

        if (!$result) {
            if (function_exists('log_console')) {
                log_console('Mailer: échec envoi email', 'error', [
                    'to' => $to,
                    'subject_length' => strlen($subject)
                ]);
            } else {
                error_log("[MAIL] [ERROR] Échec d'envoi d'email vers {$to}");
            }
        } else {
            if (function_exists('log_console')) {
                log_console('Mailer: email envoyé', 'ok', [
                    'to' => $to
                ]);
            }
        }

        return $result;
    }
}
