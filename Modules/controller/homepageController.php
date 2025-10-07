<?php
require __DIR__ . '/../view/homepageView.php';
class homePageController
{
    public function login()
    {
        ViewHandler::show("../view/homePageView");
    }
}
