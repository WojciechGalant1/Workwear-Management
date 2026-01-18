<?php
require_once __DIR__ . '/../../core/ServiceContainer.php';

class IssueController {
    
    /**
     * Formularz wydawania ubrań
     * Obsługuje GET params: ?fromRaport=1&pracownikId=X&imie=...&nazwisko=...&stanowisko=...
     */
    public function issue() {
        $serviceContainer = ServiceContainer::getInstance();
        $clothingRepo = $serviceContainer->getRepository('ClothingRepository');
        $issuedClothingRepo = $serviceContainer->getRepository('IssuedClothingRepository');
        
        $result = array(
            'pageTitle' => 'issue_title',
            'ubrania' => $clothingRepo->getAllUnique(),
            'fromRaport' => false,
            'pracownikId' => '',
            'imie' => '',
            'nazwisko' => '',
            'stanowisko' => '',
            'expiredUbrania' => array()
        );
        
        // Obsługa przekierowania z raportu
        if (isset($_GET['fromRaport']) && $_GET['fromRaport'] == '1') {
            $result['fromRaport'] = true;
            $result['pracownikId'] = isset($_GET['pracownikId']) ? htmlspecialchars($_GET['pracownikId']) : '';
            $result['imie'] = isset($_GET['imie']) ? htmlspecialchars($_GET['imie']) : '';
            $result['nazwisko'] = isset($_GET['nazwisko']) ? htmlspecialchars($_GET['nazwisko']) : '';
            $result['stanowisko'] = isset($_GET['stanowisko']) ? htmlspecialchars($_GET['stanowisko']) : '';
            
            // Pobierz wygasające ubrania dla pracownika (jedno zapytanie zamiast N+1)
            if ($result['pracownikId']) {
                $result['expiredUbrania'] = $issuedClothingRepo->getExpiringClothingByEmployeeId(
                    intval($result['pracownikId'])
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Historia wydań dla pracownika
     * Obsługuje GET parameter ?pracownikID=X
     */
    public function history() {
        $serviceContainer = ServiceContainer::getInstance();
        $pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');
        $issuedClothingRepo = $serviceContainer->getRepository('IssuedClothingRepository');
        
        $result = array(
            'pageTitle' => 'history_issue_title',
            'pracownik' => null,
            'historia' => array(),
            'pracownikNotFound' => false
        );
        
        if (isset($_GET['pracownikID']) && !empty($_GET['pracownikID'])) {
            $pracownikID = intval($_GET['pracownikID']);
            $pracownik = $pracownikRepo->getById($pracownikID);
            
            if ($pracownik) {
                $result['pracownik'] = $pracownik;
                $result['historia'] = $issuedClothingRepo->getIssueHistoryByEmployeeId($pracownikID);
            } else {
                $result['pracownikNotFound'] = true;
            }
        }
        
        return $result;
    }
}
