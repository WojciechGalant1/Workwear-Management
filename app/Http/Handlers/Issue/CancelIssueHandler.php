<?php
declare(strict_types=1);
namespace App\Http\Handlers\Issue;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;

class CancelIssueHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        $this->throttle('modify:cancel_issue', 30, 60);

        $data = $this->getJsonInput();
        if (!is_array($data)) {
            throw new ValidationException('validation_invalid_input');
        }

        if (!$this->validateCsrf($data)) {
            throw new AuthorizationException('error_csrf');
        }

        if (!isset($data['id']) || !is_numeric($data['id'])) {
            throw new ValidationException('validation_invalid_input');
        }
        
        $ubranieId = intval($data['id']);
        
        try {
            $issueService = $this->getService('IssueService');
            $issueService->cancelIssue($ubranieId);
            
            $this->successResponse();
        } catch (\Exception $e) {
            // Re-throw as generic or specific exception depending on service logic
            // Assuming service might throw valid business exceptions we want to show
            throw new \Exception($e->getMessage()); 
        }
    }
}

CancelIssueHandler::run();
