<?php
require_once __DIR__ . '/../controller/UserController.php';
require_once __DIR__ . '/../model/UserModel.php';
//require __DIR__ . '/../view/formInscriptionView.php';
class formInscriptionController
{
    public function register()
    {
        viewHandler::show("../view/formInscriptionView");
    }
}
