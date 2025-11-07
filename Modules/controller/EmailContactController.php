<?php

class EmailContactController {

    /**
     * Traite et envoie l'email de contact
     */
    public function sendContactEmail() {
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
        $destinataire = "escapethecode2025@gmail.com"; // À MODIFIER
        $sujetEmail = "Contact depuis le site : " . $sujet;

        // Corps du message en HTML
        $corpsMessage = $this->formatEmailBody($email, $sujet, $message);

        // En-têtes de l'email
        $headers = $this->prepareHeaders($email);

        // Envoyer l'email
        $emailEnvoye = mail($destinataire, $sujetEmail, $corpsMessage, $headers);

        if ($emailEnvoye) {
            return ['success' => true, 'message' => 'Votre message a été envoyé avec succès !'];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de l\'envoi du message.'];
        }
    }

    /**
     * Nettoie les entrées utilisateur
     */
    private function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    /**
     * Valide les données du formulaire
     */
    private function validateData($email, $sujet, $message) {
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
    private function formatEmailBody($email, $sujet, $message) {
        return "
        <html>
        <head>
            <title>Nouveau message de contact</title>
        </head>
        <body>
            <h2>Nouveau message de contact</h2>
            <p><strong>Email de l'expéditeur :</strong> {$email}</p>
            <p><strong>Sujet :</strong> {$sujet}</p>
            <p><strong>Message :</strong></p>
            <p>" . nl2br($message) . "</p>
            <hr>
            <p><small>Ce message a été envoyé depuis le formulaire de contact du site.</small></p>
        </body>
        </html>
        ";
    }

    /**
     * Prépare les en-têtes de l'email
     */
    private function prepareHeaders($emailExpediteur) {
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: Formulaire de contact <noreply@escapethecode.com>"; // À MODIFIER
        $headers[] = "Reply-To: {$emailExpediteur}";
        $headers[] = "X-Mailer: PHP/" . phpversion();

        return implode("\r\n", $headers);
    }

// Utilisation dans votre contrôleur User
// Dans la méthode contact() :
    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailController = new EmailContactController();
            $result = $emailController->sendContactEmail();

            if ($result['success']) {
                // Rediriger avec message de succès
                header('Location: index.php?controller=User&action=contact&success=1');
                exit;
            } else {
                // Afficher l'erreur
                $error = $result['message'];
                require_once 'views/contactView.php';
            }
        } else {
            require_once 'views/contactView.php';
        }
    }
}

// Utilisation dans votre contrôleur User
// Dans la méthode contact() :

