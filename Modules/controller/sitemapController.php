<?php
require_once __DIR__ . '/../model/sitemapModel.php';

class sitemapController
{
    private $sitemapModel;

    public function __construct()
    {
        $this->sitemapModel = new sitemapModel();
    }

    public function show()
    {
        $links = $this->sitemapModel->getLinks();
        viewHandler::show('../view/sitemapView', ['links' => $links]);
    }
}


