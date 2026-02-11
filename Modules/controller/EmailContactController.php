<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\Mailer;

class EmailContactController
{
    private static function log(string $message, string $type): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type);
        }
        // Fallback : log dans un fichier
        error_log("[{$type}] {$message}");
    }

    public function sendContactEmail(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        //Vérification méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = "Méthode non autorisée.";
            $this->redirect();
            return;
        }

        //Récupération et nettoyage
        $email   = trim($_POST['email'] ?? '');
        $sujet   = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Sauvegarde pour réaffichage en cas d'erreur
        $_SESSION['old'] = [
            'email'   => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            'sujet'   => htmlspecialchars($sujet, ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
        ];

        //  Validation
        $validation = $this->validateData($email, $sujet, $message);
        if (!$validation['valid']) {
            $_SESSION['flash_error'] = $validation['error'];
            self::log("Contact: Validation échouée - " . $validation['error'], "warning");
            $this->redirect();
            return;
        }

        // Envoi email
        try {
            $sujetFinal = "Contact Escape The Code : " . $sujet;
            $corpsEmail = $this->renderEmailTemplate([
                'email' => $email,
                'sujet' => $sujet,
                'message' => $message
            ]);

            $success = Mailer::send(self::$_ENV['ADMIN_EMAIL'], $sujetFinal, $corpsEmail);

            if ($success) {
                unset($_SESSION['old']);
                $_SESSION['flash_success'] = "Votre message a été envoyé avec succès !";
                self::log("Contact: Mail envoyé par {$email}", "ok");
            } else {
                throw new \Exception("Mailer::send a retourné false");
            }
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi de l'e-mail. Réessayez plus tard.";
            self::log("Contact: Erreur Mailer - " . $e->getMessage(), "error");
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
        return ob_get_clean();
    }

    private function redirect(): void
    {
        header("Location: index.php?controller=Redirection&action=openContact");
        exit;
    }
}
