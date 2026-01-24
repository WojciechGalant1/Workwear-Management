<?php
require_once __DIR__ . '/BaseController.php';

class WarehouseController extends BaseController {
    
    public function list(): array {
        $warehouseRepo = $this->getRepository('WarehouseRepository');
        
        return [
            'ubrania' => $warehouseRepo->readAll(),
            'pageTitle' => 'warehouse_title'
        ];
    }
}
