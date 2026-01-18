<?php
require_once __DIR__ . '/../BaseHandler.php';
require_once __DIR__ . '/../../models/Issue.php';
require_once __DIR__ . '/../../models/IssuedClothing.php';

class IssueClothingHandler extends BaseHandler {
    
    public function handle() {
        if (!$this->isPost()) {
            $this->errorResponse('error_method_not_allowed');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        $pracownikID = isset($_POST['pracownikID']) ? trim($_POST['pracownikID']) : '';
        $uwagi = isset($_POST['uwagi']) ? trim($_POST['uwagi']) : '';
        
        if (empty($pracownikID)) {
            $this->errorResponse('issue_employee_required');
        }
        
        // Walidacja pracownika
        $pracownikRepo = $this->getRepository('EmployeeRepository');
        $pracownik = $pracownikRepo->getById($pracownikID);
        
        if (!$pracownik) {
            $this->errorResponse('issue_employee_not_found');
        }
        
        // Walidacja użytkownika
        $currentUserId = $this->getUserId();
        $userRepo = $this->getRepository('UserRepository');
        $currentUser = $userRepo->getUserById($currentUserId);
        
        if (!$currentUser) {
            $this->errorResponse('error_user_not_found');
        }
        
        // Walidacja danych ubrań
        if (!isset($_POST['ubrania']) || !is_array($_POST['ubrania'])) {
            $this->errorResponse('issue_no_clothing_data');
        }
        
        $stanMagazynuRepo = $this->getRepository('WarehouseRepository');
        
        // Sprawdzenie dostępności w magazynie
        foreach ($_POST['ubrania'] as $ubranie) {
            $idUbrania = isset($ubranie['id_ubrania']) ? intval($ubranie['id_ubrania']) : 0;
            $idRozmiar = isset($ubranie['id_rozmiar']) ? intval($ubranie['id_rozmiar']) : 0;
            $ilosc = isset($ubranie['ilosc']) ? intval($ubranie['ilosc']) : 0;
            
            if ($idUbrania == 0 || $idRozmiar == 0) {
                $this->errorResponse('issue_invalid_code');
            }
            
            if ($ilosc <= 0) {
                $this->errorResponse('issue_quantity_positive');
            }
            
            $iloscDostepna = $stanMagazynuRepo->getIlosc($idUbrania, $idRozmiar);
            if ($ilosc > $iloscDostepna) {
                $this->errorResponse('issue_insufficient_stock');
            }
        }
        
        // Tworzenie wydania
        $wydaniaRepo = $this->getRepository('IssueRepository');
        $wydaneUbraniaRepo = $this->getRepository('IssuedClothingRepository');
        
        $dataWydaniaObj = new DateTime();
        $wydanie = new Issue($currentUserId, $pracownik['id_pracownik'], $dataWydaniaObj, $uwagi);
        $idWydania = $wydaniaRepo->create($wydanie);
        
        // Dodawanie ubrań i aktualizacja magazynu
        foreach ($_POST['ubrania'] as $ubranie) {
            $idUbrania = intval($ubranie['id_ubrania']);
            $idRozmiar = intval($ubranie['id_rozmiar']);
            $ilosc = intval($ubranie['ilosc']);
            $status = 1;
            
            $dataWaznosciMiesiace = isset($ubranie['data_waznosci']) ? intval($ubranie['data_waznosci']) : 0;
            $dataWaznosciObj = new DateTime();
            $dataWaznosciObj->modify("+{$dataWaznosciMiesiace} months");
            $dataWaznosci = $dataWaznosciObj->format('Y-m-d H:i:s');
            
            $wydaneUbrania = new IssuedClothing($dataWaznosci, $idWydania, $idUbrania, $idRozmiar, $ilosc, $status);
            
            if ($wydaneUbraniaRepo->create($wydaneUbrania)) {
                $stanMagazynuRepo->updateIlosc($idUbrania, $idRozmiar, $ilosc);
            } else {
                $this->errorResponse('issue_error_processing');
            }
        }
        
        $this->successResponse('issue_success');
    }
}

IssueClothingHandler::run();
