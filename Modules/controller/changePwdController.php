<?php

require_once __DIR__ . "/../model/changePwdModel.php";
class changePwdController
{
    private $changePwdModel;
    public function __construct() {
        $this->changePwdModel = new changePwdModel();
    }
    public function changePwd(){
        if (isset($_POST['changePwd'])) {
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $email = $_SESSION['email'];

            if (empty($newPassword) || empty($confirmPassword)) {
                $data['error'] = "Veuillez remplir les deux champs de mot de passe.";
                echo $data['error'];
            } elseif ($newPassword !== $confirmPassword) {
                $data['error'] = "Les mots de passe ne correspondent pas.";
                echo $data['error'];
            } else {
                if ($this->changePwdModel->changePwd($newPassword, $email)) {
                    $data['success'] = "Votre mot de passe a été modifié avec succès.";
                    echo $data['success'];
                } else {
                    $data['error'] = "Erreur lors de la modification du mot de passe.";
                    echo $data['error'];
                }
            }
        }
        viewHandler::show('changePwdView', ['pageTitle' => 'Changer de mot de passe']);
    }

}