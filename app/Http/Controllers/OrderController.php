<?php
require_once __DIR__ . '/BaseController.php';

class OrderController extends BaseController {
    
    public function history() {
        $orderHistoryRepo = $this->getRepository('OrderHistoryRepository');
        
        return [
            'zamowienia' => $orderHistoryRepo->getAll(),
            'pageTitle' => 'history_order_title'
        ];
    }
    
    public function create() {
        return [
            'pageTitle' => 'order_add_title'
        ];
    }
}
