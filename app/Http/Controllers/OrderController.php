<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Repositories\OrderHistoryRepository;

class OrderController extends BaseController {
    
    public function history(): array {
        $orderHistoryRepo = $this->getRepository(OrderHistoryRepository::class);
        
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
