<?php

declare(strict_types=1);

namespace SAE_CyberCigales_G5\includes;

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
        // Vérifier que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
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
        $data = stripslashes($data);
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
     */
    private function formatEmailBody(string $email, string $sujet, string $message): string {
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <title>Nouveau message de contact</title>
        </head>
        <body>
            <div class='container'>
                <h2>📧 Nouveau message de contact</h2>
                
                <div class='info'>
                    <p><span class='label'>Email de l'expéditeur :</span> {$email}</p>
                    <p><span class='label'>Sujet :</span> {$sujet}</p>
                </div>
                
                <div class='message-content'>
                    <p class='label'>Message :</p>
                    <p>" . nl2br($message) . "</p>
                </div>
                
                <div class='footer'>
                    <p>Ce message a été envoyé depuis le formulaire de contact du site Escape The Code.</p>
                    <p>Date : " . date('d/m/Y à H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}