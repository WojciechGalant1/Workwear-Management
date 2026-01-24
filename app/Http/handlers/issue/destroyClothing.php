<?php
require_once __DIR__ . '/../../BaseHandler.php';

class DestroyClothingHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        if (!$this->isPost()) {
            $this->errorResponse('error_method_not_allowed');
            return;
        }
        $data = $this->getJsonInput();
        if (!is_array($data)) {
            $this->errorResponse('validation_invalid_input');
            return;
        }
        if (!$this->validateCsrf($data)) {
            $this->csrfErrorResponse();
            return;
        }
        $id = intval($data['id'] ?? null);
        
        if (!$id) {
            $this->errorResponse('validation_clothing_id_required');
        }
        
        $wydaneUbraniaRepo = $this->getRepository('IssuedClothingRepository');
        
        if ($wydaneUbraniaRepo->destroyStatus($id)) {
            $this->successResponse();
        } else {
            $this->errorResponse('status_update_failed');
        }
    }
}

DestroyClothingHandler::run();
