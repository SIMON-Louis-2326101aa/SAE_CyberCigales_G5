<?php
require_once __DIR__ . "/../model/accountModel.php";
class accountController
{
    private $accountModel;
    public function __construct() {
        $this->accountModel = new accountModel();
    }
    public function account(){
        if (isset($_POST['change_pwd'])) {
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $email = $_SESSION['email'];

            if (empty($newPassword) || empty($confirmPassword)) {
                $data['error'] = "Veuillez remplir les deux champs de mot de passe.";
            } elseif ($newPassword !== $confirmPassword) {
                $data['error'] = "Les mots de passe ne correspondent pas.";
            } else {
                // Tenter la modification dans le modèle
                if ($this->accountModel->changePwd($newPassword, $email)) {
                    $data['success'] = "Votre mot de passe a été modifié avec succès.";
                } else {
                    $data['error'] = "Erreur lors de la modification du mot de passe.";
                }
            }
        }

        if (isset($_POST['delete'])) {
            $email = $_SESSION['email'];
            if ($this->accountModel->delete($email)) {
                session_destroy();
                header("Location: /index.php?controller=homepage&action=openHomepage");
                exit();
            } else {
                // Si la suppression échoue, on affiche l'erreur sur la page du compte
                $data['error'] = "Une erreur est survenue lors de la suppression de votre compte.";
            }
        }

        viewHandler::show('../view/accountView');
    }

}