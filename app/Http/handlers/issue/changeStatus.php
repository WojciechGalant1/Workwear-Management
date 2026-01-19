<?php
require_once __DIR__ . '/../../BaseHandler.php';

class ChangeStatusHandler extends BaseHandler {
    
    public function handle() {
        $data = $this->getJsonInput();
        
        if (!isset($data['id'], $data['currentStatus'])) {
            $this->errorResponse('validation_invalid_input');
        }
        
        if (!$this->validateCsrf($data)) {
            $this->csrfErrorResponse();
        }
        
        $id = intval($data['id']);
        $currentStatus = intval($data['currentStatus']);
        $newStatus = ($currentStatus == 1) ? 0 : 1;
        
        $wydaneUbraniaRepo = $this->getRepository('IssuedClothingRepository');
        
        if ($wydaneUbraniaRepo->updateStatus($id, $newStatus)) {
            $this->jsonResponse(array('success' => true, 'newStatus' => $newStatus));
        } else {
            $this->errorResponse('status_update_failed');
        }
    }
}

ChangeStatusHandler::run();
