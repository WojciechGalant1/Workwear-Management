<?php
require_once __DIR__ . '/../../BaseHandler.php';

class FetchProductNamesHandler extends BaseHandler {
    protected $requireSession = false;
    protected $requireLocalization = false;
    
    public function handle() {
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        
        $ubranieRepo = $this->getRepository('ClothingRepository');
        $ubrania = $ubranieRepo->searchByName($query);
        
        if ($ubrania === false) {
            $this->jsonResponse(array('success' => false, 'error' => 'Failed to fetch data'));
            return;
        }
        
        $this->jsonResponse($ubrania);
    }
}

FetchProductNamesHandler::run();
