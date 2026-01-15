<?php
require_once __DIR__ . '/../../core/ServiceContainer.php';

class WarehouseController {
    
    public function list() {
        $serviceContainer = ServiceContainer::getInstance();
        $warehouseRepo = $serviceContainer->getRepository('WarehouseRepository');
        
        return array(
            'ubrania' => $warehouseRepo->readAll(),
            'pageTitle' => 'warehouse_title'
        );
    }
}
