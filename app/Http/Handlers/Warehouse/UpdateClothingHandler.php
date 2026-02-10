<?php
declare(strict_types=1);
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;
use App\Services\WarehouseService;
use Exception;

class UpdateClothingHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::WAREHOUSE;
    
    public function handle(): void {
        $this->throttle('modify:update_clothing', 30, 60);
        
        if (!$this->isPost()) {
            http_response_code(405);
            throw new ValidationException('error_method_not_allowed');
        }
        
        if (!$this->validateCsrf()) {
            throw new AuthorizationException('error_csrf');
        }
        
        $currentUserId = $this->getUserId();
        
        $id = intval($_POST['id'] ?? 0);
        $nazwa = trim($_POST['nazwa'] ?? '');
        $rozmiar = trim($_POST['rozmiar'] ?? '');
        $ilosc = intval($_POST['ilosc'] ?? 0);
        $iloscMin = intval($_POST['iloscMin'] ?? 0);
        $uwagi = trim($_POST['uwagi'] ?? '');
        
        if ($id <= 0) {
            throw new ValidationException('validation_invalid_id');
        }
        
        if (empty($nazwa) || empty($rozmiar)) {
            throw new ValidationException('validation_name_size_required');
        }
        
        if ($ilosc < 0 || $iloscMin < 0) {
            throw new ValidationException('validation_quantity_negative');
        }
        
        try {
            $warehouseService = $this->getService(WarehouseService::class);
            $result = $warehouseService->updateWarehouseItem($id, $nazwa, $rozmiar, $ilosc, $iloscMin, $uwagi, $currentUserId);
            
            if ($result['success']) {
                $this->jsonResponse($result);
            } else {
                throw new Exception($result['message'] ?? 'Unknown error');
            }
        } catch (Exception $e) {
            throw new ValidationException($e->getMessage(), 0, $e);
        }
    }
}

UpdateClothingHandler::run();
