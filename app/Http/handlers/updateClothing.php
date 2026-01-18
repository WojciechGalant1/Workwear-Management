<?php
require_once __DIR__ . '/../BaseHandler.php';
require_once __DIR__ . '/../../auth/SessionManager.php';

class UpdateClothingHandler extends BaseHandler {
    
    public function handle() {
        if (!$this->isPost()) {
            http_response_code(405);
            $this->errorResponse('error_method_not_allowed');
        }
        
        if (!$this->validateCsrf()) {
            $this->csrfErrorResponse();
        }
        
        $sessionManager = new SessionManager();
        $currentUserId = $sessionManager->getUserId();
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nazwa = isset($_POST['nazwa']) ? trim($_POST['nazwa']) : '';
        $rozmiar = isset($_POST['rozmiar']) ? trim($_POST['rozmiar']) : '';
        $ilosc = isset($_POST['ilosc']) ? intval($_POST['ilosc']) : 0;
        $iloscMin = isset($_POST['iloscMin']) ? intval($_POST['iloscMin']) : 0;
        $uwagi = isset($_POST['uwagi']) ? trim($_POST['uwagi']) : '';
        
        // Walidacja
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
        
        $stanMagazynuRepo = $this->getRepository('WarehouseRepository');
        $result = $stanMagazynuRepo->updateStanMagazynu($id, $nazwa, $rozmiar, $ilosc, $iloscMin, $uwagi, $currentUserId);
        
        if ($result['success']) {
            $this->jsonResponse($result);
        } else {
            http_response_code(500);
            $this->jsonResponse($result);
        }
    }
}

UpdateClothingHandler::run();
