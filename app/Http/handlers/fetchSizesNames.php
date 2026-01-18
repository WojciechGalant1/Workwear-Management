<?php
require_once __DIR__ . '/../BaseHandler.php';

class FetchSizesNamesHandler extends BaseHandler {
    protected $requireSession = false;
    protected $requireLocalization = false;
    
    public function handle() {
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        
        $rozmiarRepo = $this->getRepository('SizeRepository');
        $rozmiary = $rozmiarRepo->searchByName($query);
        
        $this->jsonResponse($rozmiary);
    }
}

FetchSizesNamesHandler::run();
