<?php

final class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $headers = [];
        $headers[] = 'From: no-reply@escapethecode.alwaysdata.net';
        $headers[] = 'Reply-To: no-reply@escapethecode.alwaysdata.net';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        return @mail($to, $subject, $body, implode("\r\n", $headers));
    }
}


