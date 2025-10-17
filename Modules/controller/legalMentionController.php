<?php
//require __DIR__ . '/../view/legalMentionView.php';
class legalMentionController
{
    public function legal()
    {
        viewHandler::show("legalMentionView", ['pageTitle' => 'Mentions Légales']);
    }
}
