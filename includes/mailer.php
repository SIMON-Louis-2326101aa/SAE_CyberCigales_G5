<?php

final class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        // Configuration des en-têtes email
        $headers = [];
        $headers[] = 'From: no-reply@escapethecode.alwaysdata.net';
        $headers[] = 'Reply-To: no-reply@escapethecode.alwaysdata.net';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        
        // Sur AlwaysData, la fonction mail() fonctionne directement sans configuration
        // Le serveur SMTP est automatiquement configuré
        $result = @mail($to, $subject, $body, implode("\r\n", $headers));
        
        // Log pour débogage (visible dans les logs AlwaysData)
        if (!$result) {
            error_log("Échec d'envoi d'email vers $to");
        }
        
        return $result;
    }
}


