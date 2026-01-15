<?php
require_once __DIR__ . '/../../core/ServiceContainer.php';

class ReportController {
    
    public function index() {
        $serviceContainer = ServiceContainer::getInstance();
        $issuedClothingRepo = $serviceContainer->getRepository('IssuedClothingRepository');
        
        return array(
            // Tabela 1: Szczegółowa lista ubrań wygasających/przeterminowanych z danymi pracowników
            'expiringClothing' => $issuedClothingRepo->getExpiringClothingWithEmployeeDetails(),
            // Tabela 2: Podsumowanie ubrań po terminie (agregacja)
            'ubraniaPoTerminie' => $issuedClothingRepo->getUbraniaPoTerminie(),
            'pageTitle' => 'reports_issue_title'
        );
    }
}
