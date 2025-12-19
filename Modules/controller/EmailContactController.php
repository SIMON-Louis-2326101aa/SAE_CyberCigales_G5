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
     * Utilitaire de log interne (similaire au UserController)
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
        // La session est supposée démarrée dans index.php, mais par sécurité :
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = "Méthode non autorisée.";
            header("Location: index.php?controller=Redirection&action=openContact");
            exit;
        }

        // --- Vérification du jeton CSRF ---
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->_checkCsrfToken($csrfToken)) {
            $_SESSION['flash_error'] = "Requête invalide ou session expirée. Veuillez réessayer.";
            self::log("Contact: Échec CSRF", "error");
            header("Location: index.php?controller=Redirection&action=openContact");
            exit;
        }

        // Récupérer et nettoyer les données (Inspiration UserController)
        $email   = trim($_POST['email'] ?? '');
        $sujet   = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Sauvegarder les anciennes valeurs pour le formulaire en cas d'erreur
        $_SESSION['old'] = [
            'email' => htmlspecialchars($email),
            'sujet' => htmlspecialchars($sujet),
            'message' => htmlspecialchars($message)
        ];

        // Valider les données
        $validation = $this->validateData($email, $sujet, $message);
        if (!$validation['valid']) {
            $_SESSION['flash_error'] = $validation['error'];
            header("Location: index.php?controller=Redirection&action=openContact");
            exit;
        }

        // Préparer l'email
        $sujetEmail = "Contact site : " . $sujet;
        $corpsEmail = $this->formatEmailBody($email, $sujet, $message);

        // Envoyer l'email
        $emailEnvoye = Mailer::send(self::ADMIN_EMAIL, $sujetEmail, $corpsEmail);

        if ($emailEnvoye) {
            unset($_SESSION['old']); // Succès : on vide les anciens champs
            $_SESSION['flash_success'] = "Votre message a été envoyé avec succès !";
            self::log("Contact: Message envoyé de $email", "ok");
        } else {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi du message. Veuillez réessayer plus tard.";
            self::log("Contact: Échec Mailer::send", "error");
        }

        header("Location: index.php?controller=Redirection&action=openContact");
        exit;
    }

    /**
     * Valide les données du formulaire
     */
    private function validateData(string $email, string $sujet, string $message): array {
        if (empty($email) || empty($sujet) || empty($message)) {
            return ['valid' => false, 'error' => 'Tous les champs sont obligatoires.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'L\'adresse email n\'est pas valide.'];
        }

        if (strlen($sujet) < 3) {
            return ['valid' => false, 'error' => 'Le sujet doit contenir au moins 3 caractères.'];
        }

        if (strlen($message) < 10) {
            return ['valid' => false, 'error' => 'Le message est trop court (min. 10 caractères).'];
        }

        return ['valid' => true];
    }

    /**
     * Formate le corps de l'email en HTML
     */
    private function formatEmailBody(string $email, string $sujet, string $message): string {
        $date = date('d/m/Y à H:i:s');
        // Protection contre XSS dans l'email HTML
        $safeEmail = htmlspecialchars($email);
        $safeSujet = htmlspecialchars($sujet);
        $safeMessage = nl2br(htmlspecialchars($message));

        return "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
          <table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td align='center'>
            <table width='600' style='background:#ffffff; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1);'>
              <tr><td>
                <h2 style='color:#007bff; border-bottom: 1px solid #eee; padding-bottom: 10px;'>Nouveau message de contact</h2>
                <p><strong>De :</strong> {$safeEmail}</p>
                <p><strong>Sujet :</strong> {$safeSujet}</p>
                <div style='background:#f9f9f9; padding:15px; border-left:4px solid #007bff; margin:20px 0;'>
                    <p style='margin:0;'>{$safeMessage}</p>
                </div>
                <p style='font-size:12px; color:#888;'>Envoyé le {$date} depuis le formulaire de contact.</p>
              </td></tr>
            </table>
          </td></tr></table>
        </div>";
    }

    /**
     * Génère un jeton CSRF pour la vue
     */
    public function getCsrfToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie le jeton CSRF et le détruit pour empêcher le "replay"
     */
    private function _checkCsrfToken(string $token): bool {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if (empty($token) || empty($sessionToken)) {
            return false;
        }

        $valid = hash_equals($sessionToken, $token);

        // On invalide le jeton après vérification (usage unique)
        unset($_SESSION['csrf_token']);

        return $valid;
    }
}