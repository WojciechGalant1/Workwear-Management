<?php
declare(strict_types=1);
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

class FetchProductNamesHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $this->throttle('search:product_names', 60, 60);
        
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
