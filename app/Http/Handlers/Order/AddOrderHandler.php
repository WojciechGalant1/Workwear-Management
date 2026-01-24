<?php
namespace App\Http\Handlers\Order;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

class AddOrderHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::WAREHOUSE;
    
    public function handle(): void {
        if (!$this->isPost()) {
            $this->errorResponse('error_general');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        $ubrania = $_POST['ubrania'] ?? [];
        $uwagi = trim($_POST['uwagi'] ?? '');
        $currentUserId = $this->getUserId();
        
        try {
            $orderService = $this->getService('OrderService');
            
            $orderService->createOrder($currentUserId, $ubrania, $uwagi, 1);
            
            $this->successResponse('order_add_success');
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), false);
        }
    }
}

AddOrderHandler::run();
