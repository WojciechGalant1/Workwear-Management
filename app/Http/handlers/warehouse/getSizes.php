<?php
require_once __DIR__ . '/../../BaseHandler.php';

class GetSizesHandler extends BaseHandler {
    protected $requireSession = false;
    protected $requireLocalization = false;
    
    public function handle() {
        $ubranieId = isset($_GET['ubranie_id']) ? intval($_GET['ubranie_id']) : 0;
        
        $ubranieRepo = $this->getRepository('ClothingRepository');
        $rozmiary = $ubranieRepo->getRozmiaryByUbranieId($ubranieId);
        
        $this->jsonResponse($rozmiary);
    }
}

GetSizesHandler::run();
