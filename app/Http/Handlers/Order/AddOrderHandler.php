<?php
declare(strict_types=1);
namespace App\Http\Handlers\Order;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;
use Exception;

class AddOrderHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::WAREHOUSE;
    
    public function handle(): void {
        $this->throttle('modify:add_order', 30, 60);
        
        if (!$this->isPost()) {
            throw new ValidationException('error_general');
        }
        
        if (!$this->validateCsrf()) {
            throw new AuthorizationException('error_csrf');
        }
        
        $ubrania = $_POST['ubrania'] ?? [];
        $uwagi = trim($_POST['uwagi'] ?? '');
        $currentUserId = $this->getUserId();
        
        try {
            $orderService = $this->getService('OrderService');
            
            $orderService->createOrder($currentUserId, $ubrania, $uwagi, 1);
            
            $this->successResponse('order_add_success');
        } catch (Exception $e) {
            throw new ValidationException($e->getMessage(), 0, $e);
        }
    }
}

AddOrderHandler::run();
