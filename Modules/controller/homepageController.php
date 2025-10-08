<?php
//require __DIR__ . '/../view/homepageView.php';
class homePageController
{
    public function openHomepage()
    {
        viewHandler::show("../view/homepageView");
    }
}
