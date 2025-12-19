<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\Mailer;

class EmailContactController {

    private const ADMIN_EMAIL = 'escapethecode2025@gmail.com';

    private static function log(string $message, string $type): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type);
        }
        // Fallback : log dans un fichier
        error_log("[{$type}] {$message}");
    }

    public function sendContactEmail(): void {
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
            $corpsEmail = $this->formatEmailBody($email, $sujet, $message);

            $success = Mailer::send(self::ADMIN_EMAIL, $sujetFinal, $corpsEmail);

            if ($success) {
                unset($_SESSION['old']);
                $_SESSION['flash_success'] = "Votre message a été envoyé avec succès !";
                self::log("Contact: Mail envoyé par {$email}", "ok");
            } else {
                throw new \Exception("Mailer::send a retourné false");
            }
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi de l'email. Réessayez plus tard.";
            self::log("Contact: Erreur Mailer - " . $e->getMessage(), "error");
        }

        $this->redirect();
    }

    private function validateData(string $email, string $sujet, string $message): array {
        if (empty($email) || empty($sujet) || empty($message)) {
            return ['valid' => false, 'error' => 'Tous les champs sont obligatoires.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'L\'adresse email est invalide.'];
        }
        if (strlen($sujet) < 3 || strlen($sujet) > 200) {
            return ['valid' => false, 'error' => 'Le sujet doit faire entre 3 et 200 caractères.'];
        }
        if (strlen($message) < 10 || strlen($message) > 5000) {
            return ['valid' => false, 'error' => 'Le message doit faire entre 10 et 5000 caractères.'];
        }
        return ['valid' => true];
    }

    private function formatEmailBody(string $email, string $sujet, string $message): string {
        $date = date('d/m/Y à H:i:s');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safeSujet = htmlspecialchars($sujet, ENT_QUOTES, 'UTF-8');
        $safeMsg = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

        return "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
          <table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td align='center'>
            <table width='600' style='background:#ffffff; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1);'>
              <tr><td>
                <h2 style='color:#333; border-bottom: 2px solid #007bff; padding-bottom: 10px;'>Nouveau Message Contact</h2>
                <p><strong>De :</strong> {$safeEmail}</p>
                <p><strong>Sujet :</strong> {$safeSujet}</p>
                <div style='background:#f9f9f9; padding:15px; border-left:4px solid #007bff; margin:20px 0;'>
                    <p style='margin:0;'>{$safeMsg}</p>
                </div>
                <p style='font-size:12px; color:#888;'>Envoyé le {$date}</p>
              </td></tr>
            </table>
          </td></tr></table>
        </div>";
    }

    private function redirect(): void {
        header("Location: index.php?controller=Redirection&action=openContact");
        exit;
    }
}