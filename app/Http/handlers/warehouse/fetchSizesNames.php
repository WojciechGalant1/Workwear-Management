<?php
require_once __DIR__ . '/../../BaseHandler.php';

class FetchSizesNamesHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $query = $_GET['query'] ?? '';
        
        $rozmiarRepo = $this->getRepository('SizeRepository');
        $rozmiary = $rozmiarRepo->searchByName($query);
        
        $this->jsonResponse($rozmiary);
    }
}

FetchSizesNamesHandler::run();
