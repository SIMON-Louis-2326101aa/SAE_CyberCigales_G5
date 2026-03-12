<?php

namespace SAE_CyberCigales_G5\Modules\controller;

use SAE_CyberCigales_G5\includes\Constant;
use SAE_CyberCigales_G5\includes\Mailer;
use SAE_CyberCigales_G5\Modules\model\EmailVerificationModel;
use SAE_CyberCigales_G5\Modules\model\UserModel;

/**
 * Contrôleur de vérification d'email
 *
 * Gère l'envoi et la vérification des codes de vérification d'email
 * pour confirmer l'adresse email d'un utilisateur lors de l'inscription.
 *
 * @package SAE_CyberCigales\Modules\controller
 * @author Équipe CyberCigales
 */
class EmailVerificationController
{
    /**
     * Instance du modèle de vérification d'email
     *
     * @var EmailVerificationModel
     */
    private EmailVerificationModel $eModel;

    /**
     * Instance du modèle utilisateur
     *
     * @var UserModel
     */
    private UserModel $user;

    private function logEmailVerification(string $message, string $type, array $context = []): void
    {
        if (function_exists('log_console')) {
            log_console($message, $type, $context);
        }
    }

    /**
     * Constructeur du contrôleur
     *
     * Initialise les modèles nécessaires pour la vérification d'email.
     */
    public function __construct()
    {
        $this->eModel = new EmailVerificationModel();
        $this->user = new UserModel();

        $this->logEmailVerification('EmailVerificationController initialisé', 'ok');
    }

    /**
     * Génère et envoie un code de vérification par email
     *
     * @return void
     */
    public function request()
    {
        $email = $_GET['email'] ?? '';

        if (!$email) {
            $_SESSION['flash_error'] = "Adresse e-mail manquante.";

            $this->logEmailVerification('Demande code vérification refusée: email manquant', 'warn', [
                'email' => $email,
            ]);

            header('Location: index.php?controller=Redirection&action=openFormRegister');
            exit;
        }

        $this->logEmailVerification('Demande code vérification reçue', 'info', [
            'email' => $email,
        ]);

        // Toujours regénérer un code frais pour éviter un délai expiré/perdu
        $code = $this->eModel->generateAndStoreCode($email);

        $this->logEmailVerification('Code de vérification généré et stocké', 'file', [
            'email' => $email,
        ]);

        $subject = 'Vérification de votre adresse email';
        $message = $this->renderEmailTemplate([
            'code' => $code
        ]);

        $sent = Mailer::send($email, $subject, $message);

        $url = 'Location: index.php?controller=Redirection&action=openEmailVerification&email=' . urlencode($email);

        if ($sent) {
            $_SESSION['flash_success'] = "Un code vous a été envoyé.";

            $this->logEmailVerification('Code de vérification envoyé par email', 'ok', [
                'email' => $email,
            ]);
        } else {
            if (class_exists(Constant::class) && method_exists(Constant::class, 'isDev') && Constant::isDev()) {
                $_SESSION['flash_info'] = "Le mail n'a pas été envoyé. Code pour dev: {$code}";

                $this->logEmailVerification('Envoi mail vérification échoué en mode dev', 'warn', [
                    'email' => $email,
                ]);
            } else {
                $_SESSION['flash_error'] = "Erreur lors de l'envoi du code.";

                $this->logEmailVerification('Envoi mail vérification échoué', 'error', [
                    'email' => $email,
                ]);
            }
        }

        header($url);
        exit;
    }

    /**
     * Vérifie le code de vérification saisi par l'utilisateur
     *
     * @return void
     */
    public function verify()
    {
        // Nettoyage des codes expirés AVANT toute vérification
        $this->eModel->deleteExpiredCodes();

        $email = $_POST['email'] ?? '';
        $code  = $_POST['code'] ?? '';

        $this->logEmailVerification('Vérification code email demandée', 'info', [
            'email' => $email,
            'has_code' => $code !== '',
        ]);

        $errorRedirectUrl = 'Location: index.php?controller=Redirection&action=openEmailVerification&email='
            . urlencode($email);

        // Si l'un des deux manque (email ou code)
        if (!$email || !$code) {
            $_SESSION['flash_error'] = "Veuillez saisir l'e-mail et le code.";

            $this->logEmailVerification('Vérification code refusée: email ou code manquant', 'warn', [
                'email' => $email,
                'has_code' => $code !== '',
            ]);

            header($errorRedirectUrl);
            exit;
        }

        // Validation stricte: 6 chiffres
        if (!preg_match('/^[0-9]{6}$/', $code)) {
            $_SESSION['flash_error'] = "Format du code invalide (6 chiffres).";

            $this->logEmailVerification('Format de code invalide', 'warn', [
                'email' => $email,
            ]);

            header($errorRedirectUrl);
            exit;
        }

        // Vérifier le statut détaillé du code
        $codeStatus = $this->eModel->checkCodeStatus($email, $code);

        $this->logEmailVerification('Statut code vérification récupéré', 'file', [
            'email' => $email,
            'valid' => $codeStatus['valid'] ?? false,
            'reason' => $codeStatus['reason'] ?? null,
        ]);

        if ($codeStatus['valid']) {
            // Créer le compte utilisateur maintenant que l'email est vérifié
            if ($this->user->createUserAfterVerification($email)) {
                $this->eModel->deleteCode($code);

                $_SESSION['flash_success'] = "Compte créé. Vous pouvez vous connecter.";

                $this->logEmailVerification('Compte créé après vérification email', 'ok', [
                    'email' => $email,
                ]);

                header('Location: index.php?controller=Redirection&action=openFormConnection');
                exit;
            }

            $_SESSION['flash_error'] = "Erreur lors de la création du compte. Réessayez.";

            $this->logEmailVerification('Échec création compte après validation code', 'error', [
                'email' => $email,
            ]);

            header($errorRedirectUrl);
            exit;
        }

        // Afficher un message d'erreur spécifique selon la raison
        if (($codeStatus['reason'] ?? '') === 'expired') {
            $_SESSION['flash_error'] = "Le code a expiré. Veuillez demander un nouveau code.";

            $this->logEmailVerification('Code expiré', 'warn', [
                'email' => $email,
            ]);
        } else {
            $_SESSION['flash_error'] = "Le code est invalide.";

            $this->logEmailVerification('Code invalide', 'warn', [
                'email' => $email,
            ]);
        }

        header($errorRedirectUrl);
        exit;
    }

    private function renderEmailTemplate(array $data): string
    {
        extract($data);

        ob_start();
        require __DIR__ . '/../view/email/verificationEmail.php';
        return (string)ob_get_clean();
    }
}
