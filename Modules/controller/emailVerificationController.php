<?php
require_once __DIR__ . '/../model/emailVerificationModel.php';

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
        @mail($email, $subject, $message);

        viewHandler::show('../view/emailVerificationView', ['email' => $email, 'info' => 'Un code vous a été envoyé.']);
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


