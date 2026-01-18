<?php
require_once __DIR__ . '/../../core/ServiceContainer.php';

class OrderController {
    
    public function history() {
        $serviceContainer = ServiceContainer::getInstance();
        $orderHistoryRepo = $serviceContainer->getRepository('OrderHistoryRepository');
        
        return array(
            'zamowienia' => $orderHistoryRepo->getAll(),
            'pageTitle' => 'history_order_title'
        );
    }
    
    public function create() {
        return array(
            'pageTitle' => 'order_add_title'
        );
    }
}
