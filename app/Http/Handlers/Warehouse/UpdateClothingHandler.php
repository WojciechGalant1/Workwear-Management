<?php
declare(strict_types=1);
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

class UpdateClothingHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::WAREHOUSE;
    
    public function handle(): void {
        if (!$this->isPost()) {
            http_response_code(405);
            $this->errorResponse('error_method_not_allowed');
        }
        
        if (!$this->validateCsrf()) {
            $this->csrfErrorResponse();
        }
        
        $currentUserId = $this->getUserId();
        
        $id = intval($_POST['id'] ?? 0);
        $nazwa = trim($_POST['nazwa'] ?? '');
        $rozmiar = trim($_POST['rozmiar'] ?? '');
        $ilosc = intval($_POST['ilosc'] ?? 0);
        $iloscMin = intval($_POST['iloscMin'] ?? 0);
        $uwagi = trim($_POST['uwagi'] ?? '');
        
        if ($id <= 0) {
            http_response_code(400);
            $this->errorResponse('validation_invalid_id');
        }
        
        if (empty($nazwa) || empty($rozmiar)) {
            http_response_code(400);
            $this->errorResponse('validation_name_size_required');
        }
        
        if ($ilosc < 0 || $iloscMin < 0) {
            http_response_code(400);
            $this->errorResponse('validation_quantity_negative');
        }
        
        try {
            $warehouseService = $this->getService('WarehouseService');
            $result = $warehouseService->updateWarehouseItem($id, $nazwa, $rozmiar, $ilosc, $iloscMin, $uwagi, $currentUserId);
            
            if ($result['success']) {
                $this->jsonResponse($result);
            } else {
                http_response_code(500);
                $this->jsonResponse($result);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

UpdateClothingHandler::run();
