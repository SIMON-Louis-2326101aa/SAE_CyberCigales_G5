<?php
require_once __DIR__ . "/../model/accountModel.php";
class accountController
{
    private $accountModel;
    public function __construct() {
        $this->accountModel = new accountModel();
    }
    public function account(){

        if (isset($_POST['delete'])) {
            $email = $_SESSION['email'];
            if ($this->accountModel->delete($email)) {
                session_destroy();
                header("Location: /index.php?controller=homepage&action=openHomepage");
                exit();
            } else {
                $data['error'] = "Une erreur est survenue lors de la suppression de votre compte.";
                echo $data['error'];
            }
        }

        viewHandler::show('accountView', ['pageTitle' => 'Mon Compte']);
    }

}