<?php
declare(strict_types=1);
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

use App\Exceptions\ValidationException;
use App\Repositories\WarehouseRepository;

class CheckClothingExistsHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::WAREHOUSE;
    
    public function handle(): void {
        if (!isset($_GET['nazwa']) || !isset($_GET['rozmiar'])) {
            throw new ValidationException('validation_required');
        }
        
        $nazwa = $_GET['nazwa'];
        $rozmiar = $_GET['rozmiar'];
        
        $stanMagazynuRepo = $this->getRepository(WarehouseRepository::class);
        $ubranieExists = $stanMagazynuRepo->findByUbranieAndRozmiarByName($nazwa, $rozmiar);
        
        $this->jsonResponse(['exists' => (bool)$ubranieExists]);
    }
}

CheckClothingExistsHandler::run();
