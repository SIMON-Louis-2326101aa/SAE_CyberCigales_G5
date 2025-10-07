<?php
require_once __DIR__ . '/../controller/UserController.php';
require __DIR__ . '/../view/formulaireInsc.php';
class formulaireInscriptionController
{
    public function login()
    {
        ViewHandler::show("../view/formulaireInsc");
    }
}
