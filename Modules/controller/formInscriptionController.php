<?php
require_once __DIR__ . '/../model/formInscriptionModel.php';
require_once __DIR__ . '/../../includes/viewHandler.php';

//require __DIR__ . '/../view/formInscriptionView.php';
class formInscriptionController
{
    private formInscriptionModel $formInscriptionModel;
    public function __construct() {
        $this->formInscriptionModel = new formInscriptionModel();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST['email']);
            $password = $_POST['mdp'];
            $confirm = $_POST['confirm_mdp'];

            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                require __DIR__ . '/../view/formInscriptionView.php';
                return;
            }

            if ($this->formInscriptionModel->findByEmail($email)) {
                $error = "Cet email est déjà utilisé.";
                require __DIR__ . '/../view/formInscriptionView.php';
                return;
            }

            $this->formInscriptionModel->register($nom, $prenom, $email, $password);

            //header("Location: /login");
            exit;
        } else {
            viewHandler::show("../view/formInscriptionView");
        }
    }
}
