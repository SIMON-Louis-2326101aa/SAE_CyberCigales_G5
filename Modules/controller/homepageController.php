<?php
//require __DIR__ . '/../view/homepageView.php';
class homePageController
{
    public function login()
    {
        viewHandler::show("../view/homepageView");
    }
}
