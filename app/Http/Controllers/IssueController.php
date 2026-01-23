<?php
require_once __DIR__ . '/BaseController.php';

class IssueController extends BaseController {
    
    /**
     * Formularz wydawania ubrań
     * Obsługuje GET params: ?fromRaport=1&pracownikId=X&imie=...&nazwisko=...&stanowisko=...
     */
    public function issue() {
        $clothingRepo = $this->getRepository('ClothingRepository');
        $issuedClothingRepo = $this->getRepository('IssuedClothingRepository');
        
        $result = [
            'pageTitle' => 'issue_title',
            'ubrania' => $clothingRepo->getAllUnique(),
            'fromRaport' => false,
            'pracownikId' => '',
            'imie' => '',
            'nazwisko' => '',
            'stanowisko' => '',
            'expiredUbrania' => []
        ];
        
        if (isset($_GET['fromRaport']) && $_GET['fromRaport'] == '1') {
            $result['fromRaport'] = true;
            $result['pracownikId'] = isset($_GET['pracownikId']) ? htmlspecialchars($_GET['pracownikId']) : '';
            $result['imie'] = isset($_GET['imie']) ? htmlspecialchars($_GET['imie']) : '';
            $result['nazwisko'] = isset($_GET['nazwisko']) ? htmlspecialchars($_GET['nazwisko']) : '';
            $result['stanowisko'] = isset($_GET['stanowisko']) ? htmlspecialchars($_GET['stanowisko']) : '';
            
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
        $pracownikRepo = $this->getRepository('EmployeeRepository');
        $issuedClothingRepo = $this->getRepository('IssuedClothingRepository');
        
        $result = [
            'pageTitle' => 'history_issue_title',
            'pracownik' => null,
            'historia' => [],
            'pracownikNotFound' => false
        ];
        
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
