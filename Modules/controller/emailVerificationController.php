<?php
require_once __DIR__ . '/../model/emailVerificationModel.php';
require_once __DIR__ . '/../../includes/mailer.php';
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

class emailVerificationController
{
    private $eModel;
    private $user ;

    public function __construct()
    {
        $this->eModel = new emailVerificationModel();
        $this->user = new userModel();
    }

    public function request()
    {
        $email = $_GET['email'] ?? '';
        if (!$email) {
            $_SESSION['flash_error'] = "Adresse e-mail manquante.";
            header('Location: index.php?controller=redirection&action=openFormRegister');
            exit;
        }

        // Toujours regénérer un code frais pour éviter un délai expiré/perdu
        $code = $this->eModel->generateAndStoreCode($email);

        $subject = 'Vérification de votre adresse email';
        $message = "Votre code de vérification est : {$code}\nIl expire dans 10 minutes.";
        $sent = Mailer::send($email, $subject, $message);

        $params = [
            'pageTitle' => "Vérification de l'email",
            'email'     => $email,
        ];
        if ($sent) {
            $_SESSION['flash_success'] = "Un code vous a été envoyé.";
        } else {
            if (class_exists('Constant') && method_exists('Constant','isDev') && Constant::isDev()) {
                $_SESSION['flash_success'] = "Email désactivé en local. Utilisez le code affiché en console.";
                if (function_exists('log_console')) log_console("DEV code email: {$code}", 'info');
                $params['devCode'] = $code;
            } else {
                $_SESSION['flash_error'] = "L'envoi de l'email a échoué. Veuillez réessayer plus tard.";
            }
        }

        viewHandler::show('emailVerificationView', $params);
    }

    public function verify()
    {
        $email = $_POST['email'] ?? '';
        $code  = $_POST['code']  ?? '';

        if (!$email || !$code) {
            $_SESSION['flash_error'] = "Veuillez saisir l'email et le code.";
            viewHandler::show('emailVerificationView', [
                'pageTitle' => "Vérification de l'email",
                'email'     => $email
            ]);
            return;
        }

        // Validation stricte: 6 chiffres
        if (!preg_match('/^[0-9]{6}$/', $code)) {
            $_SESSION['flash_error'] = "Format du code invalide (6 chiffres).";
            viewHandler::show('emailVerificationView', [
                'pageTitle' => "Vérification de l'email",
                'email'     => $email
            ]);
            return;
        }

        // Vérifier le statut détaillé du code
        $codeStatus = $this->eModel->checkCodeStatus($email, $code);

        if ($codeStatus['valid']) {
            // Créer le compte utilisateur maintenant que l'email est vérifié
            if ($this->user->createUserAfterVerification($email)) {
                $_SESSION['flash_success'] = "Compte créé. Vous pouvez vous connecter.";
                header('Location: index.php?controller=redirection&action=openFormConnection');
                exit;
            } else {
                // Erreur lors de la création du compte
                $_SESSION['flash_error'] = "Erreur lors de la création du compte. Réessayez.";
                viewHandler::show('emailVerificationView', [
                    'pageTitle' => "Vérification de l'email",
                    'email'     => $email
                ]);
                return;
            }
        }

        // Afficher un message d'erreur spécifique selon la raison
        $_SESSION['flash_error'] = ($codeStatus['reason'] === 'expired')
            ? "Code expiré (10 minutes). <a href=\"index.php?controller=emailVerification&action=request&email=" . urlencode($email) . "\">Renvoyer un code</a>."
            : "Code incorrect. Vérifiez et réessayez.";

        viewHandler::show('emailVerificationView', [
            'pageTitle' => "Vérification de l'email",
            'email'     => $email
        ]);
    }
}

