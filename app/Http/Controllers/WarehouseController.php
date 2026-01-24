<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class WarehouseController extends BaseController {
    
    public function list(): array {
        $warehouseRepo = $this->getRepository('WarehouseRepository');
        
        return [
            'ubrania' => $warehouseRepo->readAll(),
            'pageTitle' => 'warehouse_title'
        ];
    }
}
