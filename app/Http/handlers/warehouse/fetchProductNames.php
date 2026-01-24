<?php
require_once __DIR__ . '/../../BaseHandler.php';

class FetchProductNamesHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $query = $_GET['query'] ?? '';
        
        $ubranieRepo = $this->getRepository('ClothingRepository');
        $ubrania = $ubranieRepo->searchByName($query);
        
        if ($ubrania === false) {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to fetch data']);
            return;
        }
        
        $this->jsonResponse($ubrania);
    }
}

FetchProductNamesHandler::run();
