<?php
require_once __DIR__ . '/../../BaseHandler.php';

class IssueClothingHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        if (!$this->isPost()) {
            $this->errorResponse('error_method_not_allowed');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        $pracownikID = trim($_POST['pracownikID'] ?? '');
        $uwagi = trim($_POST['uwagi'] ?? '');
        
        if (empty($pracownikID)) {
            $this->errorResponse('issue_employee_required');
        }
        
        if (!isset($_POST['ubrania']) || !is_array($_POST['ubrania'])) {
            $this->errorResponse('issue_no_clothing_data');
        }
        
        try {
            $issueService = $this->getService('IssueService');
            $currentUserId = $this->getUserId();
            
            $issueService->issueClothing(
                intval($pracownikID),
                $currentUserId,
                $_POST['ubrania'],
                $uwagi
            );
            
            $this->successResponse('issue_success');
        } catch (Exception $e) {
            $this->errorResponse($e->getMessage(), false);
        }
    }
}

IssueClothingHandler::run();
