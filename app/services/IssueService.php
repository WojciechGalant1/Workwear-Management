<?php
require_once __DIR__ . '/../entities/Issue.php';
require_once __DIR__ . '/../entities/IssuedClothing.php';
require_once __DIR__ . '/../helpers/LocalizationHelper.php';

/**
 * Serwis obsługujący logikę biznesową wydawania ubrań
 * Enkapsuluje złożoną logikę z handlerów HTTP
 */
class IssueService {
    private $serviceContainer;
    private $employeeRepo;
    private $userRepo;
    private $warehouseRepo;
    private $issueRepo;
    private $issuedClothingRepo;
    
    public function __construct($serviceContainer) {
        $this->serviceContainer = $serviceContainer;
        $this->employeeRepo = $this->serviceContainer->getRepository('EmployeeRepository');
        $this->userRepo = $this->serviceContainer->getRepository('UserRepository');
        $this->warehouseRepo = $this->serviceContainer->getRepository('WarehouseRepository');
        $this->issueRepo = $this->serviceContainer->getRepository('IssueRepository');
        $this->issuedClothingRepo = $this->serviceContainer->getRepository('IssuedClothingRepository');
    }
    
    /**
     * Wydaje ubrania pracownikowi
     * 
     * @param int $pracownikId ID pracownika
     * @param int $userId ID użytkownika wydającego
     * @param array $ubrania Tablica z danymi ubrań:
     *   - id_ubrania (int)
     *   - id_rozmiar (int)
     *   - ilosc (int)
     *   - data_waznosci (int) - liczba miesięcy
     * @param string $uwagi Opcjonalne uwagi
     * @return int ID utworzonego wydania
     * @throws Exception Gdy walidacja nie przejdzie lub wystąpi błąd
     */
    public function issueClothing($pracownikId, $userId, $ubrania, $uwagi = '') {
        // Walidacja pracownika
        $pracownik = $this->employeeRepo->getById($pracownikId);
        if (!$pracownik) {
            throw new Exception(LocalizationHelper::translate('issue_employee_not_found'));
        }
        
        // Walidacja użytkownika
        $user = $this->userRepo->getUserById($userId);
        if (!$user) {
            throw new Exception(LocalizationHelper::translate('error_user_not_found'));
        }
        
        // Walidacja danych ubrań
        if (empty($ubrania) || !is_array($ubrania)) {
            throw new Exception(LocalizationHelper::translate('issue_no_clothing_data'));
        }
        
        // Walidacja i sprawdzenie dostępności w magazynie
        $this->validateClothingData($ubrania);
        $this->validateStockAvailability($ubrania);
        
        // Tworzenie wydania
        $dataWydania = new DateTime();
        $wydanie = new Issue($userId, $pracownik['id_pracownik'], $dataWydania, $uwagi);
        $idWydania = $this->issueRepo->create($wydanie);
        
        if (!$idWydania) {
            throw new Exception(LocalizationHelper::translate('issue_error_processing'));
        }
        
        // Dodawanie ubrań i aktualizacja magazynu
        $this->addClothingToIssue($idWydania, $ubrania);
        
        return $idWydania;
    }
    
    /**
     * Waliduje dane ubrań
     * 
     * @param array $ubrania Tablica z danymi ubrań
     * @throws Exception Gdy dane są nieprawidłowe
     */
    private function validateClothingData($ubrania) {
        foreach ($ubrania as $ubranie) {
            $idUbrania = isset($ubranie['id_ubrania']) ? intval($ubranie['id_ubrania']) : 0;
            $idRozmiar = isset($ubranie['id_rozmiar']) ? intval($ubranie['id_rozmiar']) : 0;
            $ilosc = isset($ubranie['ilosc']) ? intval($ubranie['ilosc']) : 0;
            
            if ($idUbrania == 0 || $idRozmiar == 0) {
                throw new Exception(LocalizationHelper::translate('issue_invalid_code'));
            }
            
            if ($ilosc <= 0) {
                throw new Exception(LocalizationHelper::translate('issue_quantity_positive'));
            }
        }
    }
    
    /**
     * Sprawdza dostępność ubrań w magazynie
     * 
     * @param array $ubrania Tablica z danymi ubrań
     * @throws Exception Gdy brakuje ubrań w magazynie
     */
    private function validateStockAvailability($ubrania) {
        foreach ($ubrania as $ubranie) {
            $idUbrania = intval($ubranie['id_ubrania']);
            $idRozmiar = intval($ubranie['id_rozmiar']);
            $ilosc = intval($ubranie['ilosc']);
            
            $iloscDostepna = $this->warehouseRepo->getIlosc($idUbrania, $idRozmiar);
            
            if ($ilosc > $iloscDostepna) {
                throw new Exception(LocalizationHelper::translate('issue_insufficient_stock'));
            }
        }
    }
    
    /**
     * Dodaje ubrania do wydania i aktualizuje magazyn
     * 
     * @param int $idWydania ID wydania
     * @param array $ubrania Tablica z danymi ubrań
     * @throws Exception Gdy wystąpi błąd podczas dodawania
     */
    private function addClothingToIssue($idWydania, $ubrania) {
        foreach ($ubrania as $ubranie) {
            $idUbrania = intval($ubranie['id_ubrania']);
            $idRozmiar = intval($ubranie['id_rozmiar']);
            $ilosc = intval($ubranie['ilosc']);
            $status = 1; // Status aktywny
            
            // Oblicz datę ważności
            $dataWaznosciMiesiace = isset($ubranie['data_waznosci']) ? intval($ubranie['data_waznosci']) : 0;
            $dataWaznosci = $this->calculateExpiryDate($dataWaznosciMiesiace);
            
            // Utwórz wydane ubranie
            $wydaneUbranie = new IssuedClothing($dataWaznosci, $idWydania, $idUbrania, $idRozmiar, $ilosc, $status);
            
            if (!$this->issuedClothingRepo->create($wydaneUbranie)) {
                throw new Exception(LocalizationHelper::translate('issue_error_processing'));
            }
            
            // Aktualizuj magazyn (zmniejsz ilość)
            $this->warehouseRepo->updateIlosc($idUbrania, $idRozmiar, $ilosc);
        }
    }
    
    /**
     * Anuluje wydanie ubrania i zwraca je do magazynu
     * 
     * @param int $ubranieId ID wydanego ubrania
     * @throws Exception Gdy ubranie nie zostanie znalezione lub wystąpi błąd
     */
    public function cancelIssue($ubranieId) {
        $wydaneUbranie = $this->issuedClothingRepo->getUbraniaById($ubranieId);
        
        if (!$wydaneUbranie) {
            throw new Exception(LocalizationHelper::translate('clothing_issued_not_found'));
        }
        
        $idUbrania = $wydaneUbranie['id_ubrania'];
        $idRozmiaru = $wydaneUbranie['id_rozmiaru'];
        $ilosc = $wydaneUbranie['ilosc'];
        
        // Anuluj wydanie (zmień status na 3)
        if (!$this->issuedClothingRepo->deleteWydaneUbranieStatus($ubranieId)) {
            throw new Exception(LocalizationHelper::translate('cancel_issue_failed'));
        }
        
        // Zwróć do magazynu (true = dodaj z powrotem)
        $this->warehouseRepo->updateIlosc($idUbrania, $idRozmiaru, $ilosc, true);
    }
    
    /**
     * Oblicza datę ważności na podstawie liczby miesięcy
     * 
     * @param int $months Liczba miesięcy
     * @return string Data ważności w formacie Y-m-d H:i:s
     */
    private function calculateExpiryDate($months) {
        $date = new DateTime();
        $date->modify("+{$months} months");
        return $date->format('Y-m-d H:i:s');
    }
}