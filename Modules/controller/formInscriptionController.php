<?php
require_once __DIR__ . '/../controller/UserController.php';
require __DIR__ . '/../view/formInscriptionView.php';
class formInscriptionController
{
    public function login()
    {
        viewHandler::show("../view/formInscriptionView");
    }
}
