<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\Mailer;

class EmailContactController
{
    private static function log(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
            return;
        }

        error_log("[{$type}] {$message}");
    }

    public function sendContactEmail(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::log('Contact: tentative envoi formulaire', 'file', [
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);

        // ANTI-SPAM DE 5 MIN
        if (isset($_SESSION['last_email_sent_time'])) {
            $timeSinceLastEmail = time() - $_SESSION['last_email_sent_time'];

            if ($timeSinceLastEmail < 300) {
                $minutesRestantes = ceil((300 - $timeSinceLastEmail) / 60);

                $_SESSION['flash_error'] = "Veuillez patienter encore {$minutesRestantes} 
                minute(s) avant d'envoyer un nouveau message.";

                self::log('Contact: envoi bloqué par anti-spam', 'warn', [
                    'remaining_seconds' => 300 - $timeSinceLastEmail,
                    'remaining_minutes' => $minutesRestantes,
                    'user_id' => $_SESSION['user_id'] ?? null,
                ]);

                $this->redirect();
                return;
            }
        }

        // Vérification méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Méthode non autorisée.';

            self::log('Contact: méthode non autorisée', 'warn', [
                'method' => $_SERVER['REQUEST_METHOD'] ?? null,
                'uri' => $_SERVER['REQUEST_URI'] ?? null,
            ]);

            $this->redirect();
            return;
        }

        // Récupération et nettoyage
        $email   = trim($_POST['email'] ?? '');
        $sujet   = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Sauvegarde pour réaffichage en cas d'erreur
        $_SESSION['old'] = [
            'email'   => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            'sujet'   => htmlspecialchars($sujet, ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
        ];

        self::log('Contact: données formulaire reçues', 'info', [
            'email' => $email,
            'subject_length' => strlen($sujet),
            'message_length' => strlen($message),
        ]);

        // Validation
        $validation = $this->validateData($email, $sujet, $message);

        if (!$validation['valid']) {
            $_SESSION['flash_error'] = $validation['error'];

            self::log('Contact: validation échouée', 'warn', [
                'email' => $email,
                'error' => $validation['error'],
                'subject_length' => strlen($sujet),
                'message_length' => strlen($message),
            ]);

            $this->redirect();
            return;
        }

        // Envoi email
        try {
            $sujetFinal = 'Contact Escape The Code : ' . $sujet;
            $corpsEmail = $this->renderEmailTemplate([
                'email' => $email,
                'sujet' => $sujet,
                'message' => $message
            ]);

            self::log('Contact: template email généré', 'file', [
                'email' => $email,
                'final_subject_length' => strlen($sujetFinal),
                'body_length' => strlen($corpsEmail),
            ]);

            $success = Mailer::send($_ENV['ADMIN_EMAIL'], $sujetFinal, $corpsEmail);

            if ($success) {
                unset($_SESSION['old']);
                $_SESSION['flash_success'] = 'Votre message a été envoyé avec succès !';
                $_SESSION['last_email_sent_time'] = time();

                self::log('Contact: mail envoyé avec succès', 'ok', [
                    'from_email' => $email,
                    'to_admin' => $_ENV['ADMIN_EMAIL'] ?? null,
                ]);
            } else {
                throw new \Exception('Mailer::send a retourné false');
            }
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi de l'e-mail. Réessayez plus tard.";

            self::log('Contact: erreur envoi mail', 'error', [
                'from_email' => $email,
                'message' => $e->getMessage(),
            ]);
        }

        $this->redirect();
    }

    private function validateData(string $email, string $sujet, string $message): array
    {
        if (empty($email) || empty($sujet) || empty($message)) {
            return ['valid' => false, 'error' => 'Tous les champs sont obligatoires.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'L\'adresse e-mail est invalide.'];
        }

        if (strlen($sujet) < 3 || strlen($sujet) > 200) {
            return ['valid' => false, 'error' => 'Le sujet doit faire entre 3 et 200 caractères.'];
        }

        if (strlen($message) < 10 || strlen($message) > 5000) {
            return ['valid' => false, 'error' => 'Le message doit faire entre 10 et 5000 caractères.'];
        }

        return ['valid' => true];
    }

    private function renderEmailTemplate(array $data): string
    {
        extract($data);

        ob_start();
        require __DIR__ . '/../view/email/contactEmail.php';
        return (string)ob_get_clean();
    }

    private function redirect(): void
    {
        self::log('Contact: redirection vers page contact', 'file', [
            'uri' => 'index.php?controller=Redirection&action=openContact',
        ]);

        header('Location: index.php?controller=Redirection&action=openContact');
        exit;
    }
}
