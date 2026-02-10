<?php
declare(strict_types=1);
namespace App\Http\Handlers\Issue;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;
use App\Services\IssueService;
use Exception;

class IssueClothingHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $this->throttle('modify:issue_clothing', 30, 60);
        
        if (!$this->isPost()) {
            http_response_code(405);
            throw new ValidationException('error_method_not_allowed');
        }
        
        if (!$this->validateCsrf()) {
            throw new AuthorizationException('error_csrf');
        }
        
        $pracownikID = trim($_POST['pracownikID'] ?? '');
        $uwagi = trim($_POST['uwagi'] ?? '');
        
        if (empty($pracownikID)) {
            throw new ValidationException('issue_employee_required');
        }
        
        if (!isset($_POST['ubrania']) || !is_array($_POST['ubrania'])) {
            throw new ValidationException('issue_no_clothing_data');
        }
        
        try {
            $issueService = $this->getService(IssueService::class);
            $currentUserId = $this->getUserId();
            
            $issueService->issueClothing(
                intval($pracownikID),
                $currentUserId,
                $_POST['ubrania'],
                $uwagi
            );
            
            $this->successResponse('issue_success');
        } catch (Exception $e) {
            // Business logic errors from service can still be treated as 400 or 500 depending on type
            // For now, assuming ServiceException means bad input/state
            throw new ValidationException($e->getMessage(), 0, $e);
        }
    }
}

IssueClothingHandler::run();
