<?php
require_once __DIR__ . '/BaseController.php';

class ReportController extends BaseController {
    
    public function index() {
        $issuedClothingRepo = $this->getRepository('IssuedClothingRepository');
        
        return [
            'expiringClothing' => $issuedClothingRepo->getExpiringClothingWithEmployeeDetails(),
            'ubraniaPoTerminie' => $issuedClothingRepo->getUbraniaPoTerminie(),
            'pageTitle' => 'reports_issue_title'
        ];
    }
}
