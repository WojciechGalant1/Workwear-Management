<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Repositories\WarehouseRepository;

class WarehouseController extends BaseController {
    
    public function list(): array {
        $warehouseRepo = $this->getRepository(WarehouseRepository::class);
        
        return [
            'ubrania' => $warehouseRepo->readAll(),
            'pageTitle' => 'warehouse_title'
        ];
    }
}
