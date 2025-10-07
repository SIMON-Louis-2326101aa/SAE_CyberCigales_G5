<?php
require_once __DIR__ . '/../controller/UserController.php';
require __DIR__ . '/../view/formulaireConnection.php';
class homePageController
{
    public function login()
    {
        ViewHandler::show("../view/formulaireConnection");
    }
}
