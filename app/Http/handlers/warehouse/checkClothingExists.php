<?php
require_once __DIR__ . '/../../BaseHandler.php';

class CheckClothingExistsHandler extends BaseHandler {
    protected $requiredStatus = AccessLevels::WAREHOUSE;
    
    public function handle() {
        if (!isset($_GET['nazwa']) || !isset($_GET['rozmiar'])) {
            $this->errorResponse('validation_required');
        }
        
        $nazwa = $_GET['nazwa'];
        $rozmiar = $_GET['rozmiar'];
        
        $stanMagazynuRepo = $this->getRepository('WarehouseRepository');
        $ubranieExists = $stanMagazynuRepo->findByUbranieAndRozmiarByName($nazwa, $rozmiar);
        
        $this->jsonResponse(['exists' => (bool)$ubranieExists]);
    }
}

CheckClothingExistsHandler::run();
