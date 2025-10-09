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

            $to = $email;
            $subject = 'Reinitialisation du mot de passe';
            $message = 'Bonjour ! 
            Pour reinitialiser votre mot de passe cliquer sur le lien suivant: 
            https://escapethecode.alwaysdata.net/index.php?controller=forgetPwd&action=changePwd';

            if (mail($to, $subject, $message)) {
                $data['success'] = "Votre mot de passe a été modifié avec succès.";
                echo $data['success'];
            }else {
                $data['error'] = "Erreur lors de l'envoie du mail.";
                echo $data['error'];
            }
        }
        viewHandler::show('/forgotPwdView');

    }

    public function changePwd(){
        viewHandler::show('/changePwdView');
    }

}