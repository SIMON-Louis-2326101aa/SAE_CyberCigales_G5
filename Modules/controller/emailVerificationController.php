<?php
require_once __DIR__ . '/../model/emailVerificationModel.php';
require_once __DIR__ . '/../../includes/mailer.php';

class emailVerificationController
{
    private $model;

    public function __construct()
    {
        $this->model = new emailVerificationModel();
    }

    public function request()
    {
        $email = $_GET['email'] ?? '';
        if (!$email) {
            header('Location: index.php?controller=formRegister&action=register');
            exit();
        }

        $code = $this->model->generateAndStoreCode($email);

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

        viewHandler::show('../view/emailVerificationView', $params);
    }

    public function verify()
    {
        $email = $_POST['email'] ?? '';
        $code = $_POST['code'] ?? '';
        if (!$email || !$code) {
            viewHandler::show('../view/emailVerificationView', ['email' => $email, 'error' => 'Email et code requis.']);
            return;
        }

        if ($this->model->validateCode($email, $code)) {
            // Marquer l'email comme vérifié si la table users a un champ (non présent ici)
            // Rediriger vers la connexion
            header('Location: index.php?controller=formConnection&action=login');
            exit();
        }

        viewHandler::show('../view/emailVerificationView', ['email' => $email, 'error' => 'Code invalide ou expiré.']);
    }
}


