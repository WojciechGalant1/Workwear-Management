<?php
require_once __DIR__ . '/../../core/ServiceContainer.php';

class ClothingController {
    
    public function history() {
        $serviceContainer = ServiceContainer::getInstance();
        $issuedClothingRepo = $serviceContainer->getRepository('IssuedClothingRepository');
        
        return array(
            'data' => $issuedClothingRepo->getWydaneUbraniaWithDetails(),
            'pageTitle' => 'history_clothing_title'
        );
    }
}
