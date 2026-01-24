<?php
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

class CheckClothingExistsHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::WAREHOUSE;
    
    public function handle(): void {
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
