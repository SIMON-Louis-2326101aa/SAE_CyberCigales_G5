<?php
require_once __DIR__ . '/../model/formRegisterModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

//require __DIR__ . '/../view/formRegisterView.php';
class formRegisterController
{
    private $formInscriptionModel;
    public function __construct() {
        $this->formInscriptionModel = new formRegisterModel();
    }

    public function register() {
        if (isset($_POST['register'])) {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['pwd'] ?? '';
            $confirm = $_POST['confirm_pwd'] ?? '';

            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                viewHandler::show("../view/formRegisterView", ['error' => $error]);
                echo $error;
                return;
            }

            // Vérifier le statut de l'email
            $emailStatus = $this->formInscriptionModel->getEmailStatus($email);
            
            if ($emailStatus['verified']) {
                // Le compte existe et est vérifié
                $error = "Un compte avec cet email existe déjà. <a href='index.php?controller=formConnection&action=login'>Connectez-vous ici</a> ou utilisez un autre email.";
                viewHandler::show("../view/formRegisterView", ['error' => $error]);
                return;
            } elseif ($emailStatus['pending']) {
                // Une inscription est en attente de vérification
                $error = "Une inscription est déjà en cours pour cet email. <a href='index.php?controller=emailVerification&action=request&email=" . urlencode($email) . "'>Cliquez ici pour recevoir un nouveau code</a>.";
                viewHandler::show("../view/formRegisterView", ['error' => $error]);
                return;
            }

            // Stocker l'inscription en attente au lieu de créer le compte immédiatement
            require_once __DIR__ . '/../model/emailVerificationModel.php';
            $emailModel = new emailVerificationModel();
            $success = $emailModel->storePendingRegistration($nom, $prenom, $email, password_hash($password, PASSWORD_BCRYPT));

            if ($success) {
                // Après stockage temporaire, rediriger vers la demande de code
                header('Location: index.php?controller=emailVerification&action=request&email=' . urlencode($email));
                exit();
            } else {
                $error = "Erreur lors de l'inscription.";
                ViewHandler::show('../view/formRegisterView', ['error' => $error]);
                echo $error;
                return;
            }
        }
        viewHandler::show("../view/formRegisterView");

    }
}
