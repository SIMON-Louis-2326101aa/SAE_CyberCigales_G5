<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\Mailer;

class EmailContactController {

    /**
     * Adresse email du destinataire
     */
    private const ADMIN_EMAIL = 'escapethecode2025@gmail.com';

    /**
     * Traite et envoie l'email de contact
     */
    public function sendContactEmail(): array {
        // Démarre la session (doit être fait avant toute sortie)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée.'];
        }

        // --- CORRECTION 1: Vérification du jeton CSRF ---
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->_checkCsrfToken($csrfToken)) {
            return ['success' => false, 'message' => 'Requête invalide. Veuillez réessayer.'];
        }

        // Récupérer et nettoyer les données du formulaire
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $sujet = $this->sanitizeInput($_POST['sujet'] ?? '');
        $message = $this->sanitizeInput($_POST['message'] ?? '');

        // Valider les données
        $validation = $this->validateData($email, $sujet, $message);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['error']];
        }

        // Préparer l'email
        $sujetEmail = "Contact depuis le site : " . $sujet;
        $corpsMessage = $this->formatEmailBody($email, $sujet, $message);

        // Envoyer l'email avec votre classe Mailer
        // NOTE: La classe Mailer::send doit être correctement configurée
        $emailEnvoye = Mailer::send(self::ADMIN_EMAIL, $sujetEmail, $corpsMessage);

        if ($emailEnvoye) {
            return ['success' => true, 'message' => 'Votre message a été envoyé avec succès !'];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de l\'envoi du message. Veuillez réessayer.'];
        }
    }

    /**
     * Nettoie les entrées utilisateur
     */
    private function sanitizeInput(string $data): string {
        $data = trim($data);
        // --- CORRECTION 2: Suppression de stripslashes() (obsolète/souvent inutile) ---
        // $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
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
            return ['valid' => false, 'error' => 'Le message doit contenir au moins 10 caractères.'];
        }

        return ['valid' => true];
    }

    /**
     * Formate le corps de l'email en HTML
     * CORRECTION 3: Utilisation de styles en ligne pour une meilleure compatibilité dans les clients de messagerie.
     */
    private function formatEmailBody(string $email, string $sujet, string $message): string {
        $date = date('d/m/Y à H:i:s');
        $message_formate = nl2br(htmlspecialchars($message)); // S'assurer que le message est bien encodé

        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <title>Nouveau message de contact</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; background-color: #f7f7f7;'>
                <h2 style='color: #0056b3; border-bottom: 2px solid #eee; padding-bottom: 10px;'>📧 Nouveau message de contact</h2>
                
                <div style='margin-bottom: 20px; padding: 10px; background-color: #fff; border: 1px solid #eee;'>
                    <p><span style='font-weight: bold; color: #555;'>Email de l'expéditeur :</span> <a href='mailto:{$email}'>{$email}</a></p>
                    <p><span style='font-weight: bold; color: #555;'>Sujet :</span> {$sujet}</p>
                </div>
                
                <div style='padding: 15px; border-left: 3px solid #0056b3; background-color: #fff;'>
                    <p style='font-weight: bold; margin-top: 0; color: #555;'>Message :</p>
                    <p style='white-space: pre-wrap; margin-bottom: 0;'>{$message_formate}</p>
                </div>
                
                <div style='margin-top: 25px; font-size: 0.8em; color: #777; border-top: 1px solid #eee; padding-top: 10px;'>
                    <p>Ce message a été envoyé depuis le formulaire de contact du site Escape The Code.</p>
                    <p>Date : {$date}</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Fonction utilitaire pour générer et stocker le jeton CSRF.
     * Doit être appelée par le contrôleur qui affiche la vue.
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
     * Vérifie le jeton CSRF posté par le formulaire.
     */
    private function _checkCsrfToken(string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $session_token = $_SESSION['csrf_token'] ?? '';

        // Utilisation de hash_equals pour une comparaison sécurisée contre les attaques temporelles
        $valid = (!empty($token) && !empty($session_token) && hash_equals($session_token, $token));

        // Suppression du jeton de session pour prévenir la réutilisation (important)
        unset($_SESSION['csrf_token']);

        return $valid;
    }
}