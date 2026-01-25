<?php
declare(strict_types=1);
namespace App\Http\Handlers\Employee;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;

class UpdateEmployeeHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        $this->throttle('modify:update_employee', 30, 60);
        
        if (!$this->isPost()) {
            throw new ValidationException('error_general');
        }
        
        if (!$this->validateCsrf()) {
            throw new AuthorizationException('error_csrf');
        }
        
        $id = intval($_POST['id'] ?? 0);
        $imie = trim($_POST['imie'] ?? '');
        $nazwisko = trim($_POST['nazwisko'] ?? '');
        $stanowisko = trim($_POST['stanowisko'] ?? '');
        $status = intval($_POST['status'] ?? -1);
        
        if (empty($id) || empty($imie) || empty($nazwisko) || empty($stanowisko) || $status < 0) {
            throw new ValidationException('validation_required');
        }
        
        if (!preg_match('/^[\p{L}\s-]+$/u', $imie) || !preg_match('/^[\p{L}\s-]+$/u', $nazwisko)) {
            throw new ValidationException('validation_name_invalid_characters');
        }
        
        $pracownikRepo = $this->getRepository('EmployeeRepository');
        
        if ($pracownikRepo->update($id, $imie, $nazwisko, $stanowisko, $status)) {
            $this->successResponse('employee_update_success');
        } else {
            throw new \Exception('Failed to update employee');
        }
    }
}

UpdateEmployeeHandler::run();
