<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\Mailer;

class EmailContactController {

    /**
     * Adresse email du destinataire (Admin)
     */
    private const ADMIN_EMAIL = 'escapethecode2025@gmail.com';

    /**
     * Utilitaire de log interne (identique au UserController)
     */
    private static function log(string $message, string $type): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type);
        }
    }

    /**
     * Traite et envoie l'email de contact
     */
    public function sendContactEmail(): void {
        // La session est normalement gérée globalement, mais on vérifie par sécurité
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Vérification de la méthode POST (comme dans UserController::register)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = "Méthode non autorisée.";
            header("Location: index.php?controller=Redirection&action=openContact");
            exit;
        }

        // 2. Vérification du jeton CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->_checkCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Requête invalide. Veuillez réessayer.";
            self::log("Contact: Échec CSRF", "error");
            header("Location: index.php?controller=Redirection&action=openContact");
            exit;
        }

        // 3. Récupération et nettoyage (Inspiration UserController)
        $email   = trim($_POST['email'] ?? '');
        $sujet   = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // On garde les valeurs en session pour ne pas vider le formulaire en cas d'erreur
        $_SESSION['old'] = [
            'email'   => htmlspecialchars($email),
            'sujet'   => htmlspecialchars($sujet),
            'message' => htmlspecialchars($message)
        ];

        // 4. Validation des données
        $validation = $this->validateData($email, $sujet, $message);
        if (!$validation['valid']) {
            $_SESSION['flash_error'] = $validation['error'];
            header("Location: index.php?controller=Redirection&action=openContact");
            exit;
        }

        // 5. Préparation et envoi
        $sujetFinal = "Contact Escape The Code : " . $sujet;
        $corpsEmail = $this->formatEmailBody($email, $sujet, $message);

        $success = Mailer::send(self::ADMIN_EMAIL, $sujetFinal, $corpsEmail);

        if ($success) {
            unset($_SESSION['old']); // On nettoie le formulaire après succès
            $_SESSION['flash_success'] = "Votre message a été envoyé avec succès !";
            self::log("Contact: Mail envoyé par $email", "ok");
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi de l'email. Réessayez plus tard.";
            self::log("Contact: Échec Mailer::send", "error");
        }

        // Redirection pour éviter le renvoi du formulaire (Pattern Post-Redirect-Get)
        header("Location: index.php?controller=Redirection&action=openContact");
        exit;
    }

    private function validateData(string $email, string $sujet, string $message): array {
        if (empty($email) || empty($sujet) || empty($message)) {
            return ['valid' => false, 'error' => 'Tous les champs sont obligatoires.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'L\'adresse email est invalide.'];
        }
        if (strlen($sujet) < 3) {
            return ['valid' => false, 'error' => 'Le sujet doit faire au moins 3 caractères.'];
        }
        if (strlen($message) < 10) {
            return ['valid' => false, 'error' => 'Le message doit faire au moins 10 caractères.'];
        }
        return ['valid' => true];
    }

    private function formatEmailBody(string $email, string $sujet, string $message): string {
        $date = date('d/m/Y à H:i:s');
        $safeEmail = htmlspecialchars($email);
        $safeSujet = htmlspecialchars($sujet);
        $safeMsg = nl2br(htmlspecialchars($message));

        // Design inspiré des mails du UserController
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

    public function getCsrfToken(): string {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function _checkCsrfToken(string $token): bool {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if (empty($token) || empty($sessionToken)) return false;

        $isValid = hash_equals($sessionToken, $token);
        unset($_SESSION['csrf_token']); // Protection contre le replay
        return $isValid;
    }
}