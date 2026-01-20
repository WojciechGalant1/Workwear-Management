<?php
require_once __DIR__ . '/../../BaseHandler.php';

class DestroyClothingHandler extends BaseHandler {
    protected $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle() {
        if (!$this->isPost()) {
            $this->errorResponse('error_method_not_allowed');
        }
        
        $data = $this->getJsonInput();
        
        if (!$this->validateCsrf($data)) {
            $this->csrfErrorResponse();
        }
        
        $id = isset($data['id']) ? intval($data['id']) : null;
        
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
