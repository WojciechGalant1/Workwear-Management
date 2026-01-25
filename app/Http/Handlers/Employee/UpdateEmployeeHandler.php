<?php
declare(strict_types=1);
namespace App\Http\Handlers\Employee;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

class UpdateEmployeeHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        if (!$this->isPost()) {
            $this->errorResponse('error_general');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        $id = intval($_POST['id'] ?? 0);
        $imie = trim($_POST['imie'] ?? '');
        $nazwisko = trim($_POST['nazwisko'] ?? '');
        $stanowisko = trim($_POST['stanowisko'] ?? '');
        $status = intval($_POST['status'] ?? -1);
        
        if (empty($id) || empty($imie) || empty($nazwisko) || empty($stanowisko) || $status < 0) {
            $this->errorResponse('validation_required');
        }
        
        $pracownikRepo = $this->getRepository('EmployeeRepository');
        
        if ($pracownikRepo->update($id, $imie, $nazwisko, $stanowisko, $status)) {
            $this->successResponse('employee_update_success');
        } else {
            $this->errorResponse('error_general');
        }
    }
}

UpdateEmployeeHandler::run();
