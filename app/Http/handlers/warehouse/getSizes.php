<?php
require_once __DIR__ . '/../../BaseHandler.php';

class GetSizesHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $ubranieId = intval($_GET['ubranie_id'] ?? 0);
        
        $ubranieRepo = $this->getRepository('ClothingRepository');
        $rozmiary = $ubranieRepo->getRozmiaryByUbranieId($ubranieId);
        
        $this->jsonResponse($rozmiary);
    }
}

GetSizesHandler::run();
