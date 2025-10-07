<?php
require_once __DIR__ . '/../controller/UserController.php';
require __DIR__ . '/../view/formInscriptionView.php';
class formulaireInscriptionController
{
    public function login()
    {
        viewHandler::show("../view/formulaireInsc");
    }
}
