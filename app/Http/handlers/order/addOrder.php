<?php
require_once __DIR__ . '/../../BaseHandler.php';

class AddOrderHandler extends BaseHandler {
    
    public function handle() {
        if (!$this->isPost()) {
            $this->errorResponse('error_general');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        $ubrania = isset($_POST['ubrania']) ? $_POST['ubrania'] : array();
        $uwagi = isset($_POST['uwagi']) ? trim($_POST['uwagi']) : '';
        $currentUserId = $this->getUserId();
        
        try {
            $orderService = $this->getService('OrderService');
            
            $orderService->createOrder($currentUserId, $ubrania, $uwagi, 1);
            
            $this->successResponse('order_add_success');
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), false);
        }
    }
}

AddOrderHandler::run();
