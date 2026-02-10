<?php
declare(strict_types=1);
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Repositories\SizeRepository;

class FetchSizesNamesHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $this->throttle('search:size_names', 60, 60);
        
        $query = $_GET['query'] ?? '';
        
        $rozmiarRepo = $this->getRepository(SizeRepository::class);
        $rozmiary = $rozmiarRepo->searchByName($query);
        
        $this->jsonResponse($rozmiary);
    }
}

FetchSizesNamesHandler::run();
