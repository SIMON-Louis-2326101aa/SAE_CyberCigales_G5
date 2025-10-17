<?php

require_once __DIR__ . "/../model/forgotPwdModel.php";
class forgotPwdController
{
    private $forgotPwdModel;
    public function __construct() {
        $this->forgotPwdModel = new forgotPwdModel();
    }
    public function forgot(){
        if (isset($_POST['forgotPwd'])) {
            $email = $_POST['email'] ?? '';
            if (!$this->forgotPwdModel->emailExists($email)) {
                $data['error'] = "L'email n'existe pas ! Veuillez retourner en arriere pour vous inscrire.";
                echo $data['error'];
            } else {
                $_SESSION['email'] = $email;

                $to = $email;
                $subject = 'Reinitialisation du mot de passe';
                $message = 'Bonjour ! 
                Pour reinitialiser votre mot de passe cliquer sur le lien suivant: 
                https://escapethecode.alwaysdata.net/index.php?controller=forgotPwd&action=changePwd';

                if (mail($to, $subject, $message)) {
                    $data['success'] = "Un lien de réinitialisation vous a été envoyé.";
                    echo $data['success'];
                } else {
                    $data['error'] = "Erreur lors de l'envoie du mail. Veuillez réessayer.";
                    echo $data['error'];
                }
            }
        }
        viewHandler::show('forgotPwdView', ['pageTitle' => 'Mot de passe oublié']);

    }

    public function changePwd(){

        viewHandler::show('changePwdView', ['pageTitle' => 'Changer de mot de passe']);
    }

}