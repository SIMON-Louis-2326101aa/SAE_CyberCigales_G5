<?php
require_once __DIR__ . '/../controller/UserController.php';
require __DIR__ . '/../view/formConnectionView.php';
class formConnectionController
{
    public function login()
    {
        viewHandler::show("../view/formConnectionView");
    }
}
