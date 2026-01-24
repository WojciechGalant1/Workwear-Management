<?php
require_once __DIR__ . '/../../BaseHandler.php';

class ChangeStatusHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        $data = $this->getJsonInput();
        if (!is_array($data)) {
            $this->errorResponse('validation_invalid_input');
            return;
        }
        if (!isset($data['id'], $data['currentStatus'])) {
            $this->errorResponse('validation_invalid_input');
            return;
        }
        if (!$this->validateCsrf($data)) {
            $this->csrfErrorResponse();
            return;
        }
        $id = intval($data['id']);
        $currentStatus = intval($data['currentStatus']);
        $newStatus = ($currentStatus == 1) ? 0 : 1;
        
        $wydaneUbraniaRepo = $this->getRepository('IssuedClothingRepository');
        
        if ($wydaneUbraniaRepo->updateStatus($id, $newStatus)) {
            $this->jsonResponse(['success' => true, 'newStatus' => $newStatus]);
        } else {
            $this->errorResponse('status_update_failed');
        }
    }
}

ChangeStatusHandler::run();
