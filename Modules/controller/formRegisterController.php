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

            if ($this->formInscriptionModel->findByEmail($email)) {
                $error = "Impossible de créer le compte. Veuillez vérifier les informations saisies.";
                viewHandler::show("../view/formRegisterView", ['error' => $error]);
                echo $error;
                return;
            }

            $success = $this->formInscriptionModel->register($nom, $prenom, $email, $password);

            if ($success) {
                // Après inscription, envoyer un code de vérification et rediriger vers la page de saisie
                require_once __DIR__ . '/../model/emailVerificationModel.php';
                $evm = new emailVerificationModel();
                $evm->generateAndStoreCode($email);
                @mail($email, 'Vérification de votre adresse email', "Votre code de vérification vient d'être envoyé.");

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
