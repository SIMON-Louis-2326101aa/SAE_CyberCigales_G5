<?php
require_once __DIR__ . '/../controller/UserController.php';
require __DIR__ . '/../view/formulaireConnection.php';
class formulaireConnectionController
{
    public function login()
    {
        viewHandler::show("../view/formulaireConnection");
    }
}
