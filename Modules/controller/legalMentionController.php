<?php
//require __DIR__ . '/../view/legalMentionView.php';
class legalMentionController
{
    public function legal()
    {
        viewHandler::show("../view/legalMentionView");
    }
}
