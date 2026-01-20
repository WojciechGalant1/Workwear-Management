<?php
require_once __DIR__ . '/../../BaseHandler.php';

class UpdateEmployeeHandler extends BaseHandler {
    protected $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle() {
        if (!$this->isPost()) {
            $this->errorResponse('error_general');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $imie = isset($_POST['imie']) ? trim($_POST['imie']) : '';
        $nazwisko = isset($_POST['nazwisko']) ? trim($_POST['nazwisko']) : '';
        $stanowisko = isset($_POST['stanowisko']) ? trim($_POST['stanowisko']) : '';
        $status = isset($_POST['status']) ? intval($_POST['status']) : -1;
        
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
