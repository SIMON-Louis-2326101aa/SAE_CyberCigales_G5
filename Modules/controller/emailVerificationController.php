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
            header('Location: index.php?controller=formRegister&action=register');
            exit();
        }

        // Toujours regénérer un code frais pour éviter un délai expiré/perdu
        $code = $this->eModel->generateAndStoreCode($email);

        $subject = 'Vérification de votre adresse email';
        $message = "Votre code de vérification est : {$code}\nIl expire dans 10 minutes.";
        $sent = Mailer::send($email, $subject, $message);

        $params = ['email' => $email];
        if ($sent) {
            $params['info'] = 'Un code vous a été envoyé.';
        } else {
            // En dev/local uniquement, afficher le code pour permettre les tests
            if (class_exists('Constant') && Constant::isDev()) {
                $params['info'] = "Envoi d'email indisponible en local. Utilisez le code affiché ci-dessous.";
                $params['devCode'] = $code;
            } else {
                $params['error'] = "L'envoi de l'email a échoué. Veuillez réessayer plus tard.";
            }
        }

        viewHandler::show('../view/emailVerificationView', ['pageTitle' => 'Vérication de l\'email']);
    }

    public function verify()
    {
        $email = $_POST['email'] ?? '';
        $code = $_POST['code'] ?? '';
        if (!$email || !$code) {
            viewHandler::show('../view/emailVerificationView', ['pageTitle' => 'Vérication de l\'email']);
            return;
        }

        // Validation stricte: 6 chiffres
        //'error' => 'Veuillez respecter le format: 6 chiffres.']
        if (!preg_match('/^[0-9]{6}$/', $code)) {
            viewHandler::show('../view/emailVerificationView',['pageTitle' => 'Vérication de l\'email']);
            return;
        }

        // Vérifier le statut détaillé du code
        $codeStatus = $this->eModel->checkCodeStatus($email, $code);

        if ($codeStatus['valid']) {
            // Créer le compte utilisateur maintenant que l'email est vérifié
            if ($this->user->createUserAfterVerification($email)) {
                // Compte créé avec succès, rediriger vers la connexion avec message de succès
                header('Location: index.php?controller=user&action=login&registered=success');
                exit();
            } else {
                // Erreur lors de la création du compte
                viewHandler::show('../view/emailVerificationView', ['pageTitle' => 'Vérication de l\'email'],['email' => $email, 'error' => 'Erreur lors de la création du compte. Veuillez réessayer.']);
                return;
            }
        }

        // Afficher un message d'erreur spécifique selon la raison
        if ($codeStatus['reason'] === 'expired') {
            viewHandler::show('../view/emailVerificationView', [
                'email' => $email,
                'error' => 'Ce code a expiré. Les codes sont valables 10 minutes. <a href="index.php?controller=emailVerification&action=request&email=' . urlencode($email) . '">Demander un nouveau code</a>.'
            ]);
        } else {
            viewHandler::show('../view/emailVerificationView', [
                'email' => $email,
                'error' => 'Le code saisi est incorrect. Veuillez vérifier et réessayer.'
            ]);
        }
    }
}

