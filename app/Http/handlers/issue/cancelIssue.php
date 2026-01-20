<?php
require_once __DIR__ . '/../../BaseHandler.php';

class CancelIssueHandler extends BaseHandler {
    protected $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle() {
        $data = $this->getJsonInput();
        
        if (!$this->validateCsrf($data)) {
            $this->csrfErrorResponse();
        }
        
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            $this->errorResponse('validation_invalid_input');
        }
        
        try {
            $issueService = $this->getService('IssueService');
            $ubranieId = intval($data['id']);
            
            $issueService->cancelIssue($ubranieId);
            
            $this->successResponse();
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), false);
        }
    }
}

CancelIssueHandler::run();
