<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class OrderController extends BaseController {
    
    public function history(): array {
        $orderHistoryRepo = $this->getRepository('OrderHistoryRepository');
        
        return [
            'zamowienia' => $orderHistoryRepo->getAll(),
            'pageTitle' => 'history_order_title'
        ];
    }
    
    public function create(): array {
        return [
            'pageTitle' => 'order_add_title'
        ];
    }
}
