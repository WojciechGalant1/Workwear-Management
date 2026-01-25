<?php
declare(strict_types=1);
namespace App\Http\Handlers\Issue;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

class CancelIssueHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        $data = $this->getJsonInput();
        if (!is_array($data)) {
            $this->errorResponse('validation_invalid_input');
            return;
        }
        if (!$this->validateCsrf($data)) {
            $this->csrfErrorResponse();
            return;
        }
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            $this->errorResponse('validation_invalid_input');
            return;
        }
        
        try {
            $issueService = $this->getService('IssueService');
            $ubranieId = intval($data['id']);
            
            $issueService->cancelIssue($ubranieId);
            
            $this->successResponse();
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), false);
        }
    }
}

CancelIssueHandler::run();
