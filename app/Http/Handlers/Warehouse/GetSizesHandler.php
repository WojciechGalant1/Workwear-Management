<?php
declare(strict_types=1);
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

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
