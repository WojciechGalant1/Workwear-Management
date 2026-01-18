<?php
require_once __DIR__ . '/../BaseHandler.php';

class CancelIssueHandler extends BaseHandler {
    
    public function handle() {
        $data = $this->getJsonInput();
        
        if (!$this->validateCsrf($data)) {
            $this->csrfErrorResponse();
        }
        
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            $this->errorResponse('validation_invalid_input');
        }
        
        $ubranieId = intval($data['id']);
        
        $wydaneUbraniaRepo = $this->getRepository('IssuedClothingRepository');
        $stanMagazynuRepo = $this->getRepository('WarehouseRepository');
        
        $wydaneUbranie = $wydaneUbraniaRepo->getUbraniaById($ubranieId);
        
        if (!$wydaneUbranie) {
            $this->errorResponse('clothing_issued_not_found');
        }
        
        $idUbrania = $wydaneUbranie['id_ubrania'];
        $idRozmiaru = $wydaneUbranie['id_rozmiaru'];
        $ilosc = $wydaneUbranie['ilosc'];
        
        if ($wydaneUbraniaRepo->deleteWydaneUbranieStatus($ubranieId)) {
            // Zwróć do magazynu
            $stanMagazynuRepo->updateIlosc($idUbrania, $idRozmiaru, $ilosc, true);
            $this->successResponse();
        } else {
            $this->errorResponse('cancel_issue_failed');
        }
    }
}

CancelIssueHandler::run();
