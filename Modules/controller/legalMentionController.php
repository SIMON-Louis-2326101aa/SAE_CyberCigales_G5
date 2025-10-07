<?php
require __DIR__ . '/../view/legalMentionView.php';
class legalMentionController
{
    public function login()
    {
        viewHandler::show("../view/legalMentionView");
    }
}
