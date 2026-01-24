<?php
include_once __DIR__ . '/BaseRepository.php';
include_once __DIR__ . '/../entities/IssuedClothing.php';

class IssuedClothingRepository extends BaseRepository {

    private object $expiryService;

    public function __construct(PDO $pdo, object $expiryService)
    {
        parent::__construct($pdo);
        $this->expiryService = $expiryService;
    }

    public function create(IssuedClothing $wydaneUbrania): bool {
        $stmt = $this->pdo->prepare("INSERT INTO wydane_ubrania (id_wydania, id_ubrania, id_rozmiaru, ilosc, data_waznosci, status) VALUES (:id_wydania, :id_ubrania, :id_rozmiaru, :ilosc, :data_waznosci, :status)");
        $stmt->bindValue(':id_wydania', $wydaneUbrania->getIdWydania());
        $stmt->bindValue(':id_ubrania', $wydaneUbrania->getIdUbrania());
        $stmt->bindValue(':id_rozmiaru', $wydaneUbrania->getIdRozmiaru());
        $stmt->bindValue(':ilosc', $wydaneUbrania->getIlosc());
        $dataWaznosci = $wydaneUbrania->getDataWaznosci();
        $stmt->bindValue(':data_waznosci', $dataWaznosci ? $dataWaznosci->format('Y-m-d') : null);
        $stmt->bindValue(':status', $wydaneUbrania->getStatus());
        return $stmt->execute();
    }
     
    public function getUbraniaByWydanieId(int $id_wydania): array {
        $stmt = $this->pdo->prepare("SELECT wu.id, wu.ilosc, wu.data_waznosci, wu.status, wu.id_ubrania, u.nazwa_ubrania, r.nazwa_rozmiaru,
            CASE 
                WHEN wu.data_waznosci <= :currentDate THEN 1
                WHEN wu.data_waznosci <= :twoMonthsAhead THEN 1
                ELSE 0
            END AS canBeReported
        FROM wydane_ubrania wu
        LEFT JOIN ubranie u ON wu.id_ubrania = u.id_ubranie
        LEFT JOIN rozmiar r ON wu.id_rozmiaru = r.id_rozmiar
        WHERE wu.id_wydania = :id_wydania");
    
        $stmt->bindValue(':id_wydania', $id_wydania);
        $stmt->bindValue(':currentDate', $this->expiryService->getCurrentDateFormatted());
        $stmt->bindValue(':twoMonthsAhead', $this->expiryService->getExpiryWarningDateFormatted());
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, int $newStatus): bool {
        $stmt = $this->pdo->prepare("UPDATE wydane_ubrania SET status = :newStatus WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':newStatus', $newStatus, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function destroyStatus(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE wydane_ubrania SET status = 2, data_waznosci = :current_date WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':current_date', date('Y-m-d'), PDO::PARAM_STR);
    
        return $stmt->execute();
    }

    public function deleteWydaneUbranieStatus(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE wydane_ubrania SET status = 3 WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUbraniaPoTerminie(): array {
        $stmt = $this->pdo->prepare("SELECT ubranie.nazwa_ubrania, rozmiar.nazwa_rozmiaru, SUM(wydane_ubrania.ilosc) AS ilosc,
                   stan_magazynu.ilosc AS ilosc_magazyn, stan_magazynu.iloscMin AS ilosc_min
            FROM wydane_ubrania
            JOIN ubranie ON wydane_ubrania.id_ubrania = ubranie.id_ubranie
            JOIN rozmiar ON wydane_ubrania.id_rozmiaru = rozmiar.id_rozmiar
            JOIN stan_magazynu ON wydane_ubrania.id_ubrania = stan_magazynu.id_ubrania
               AND wydane_ubrania.id_rozmiaru = stan_magazynu.id_rozmiaru
            WHERE (wydane_ubrania.data_waznosci <= :currentDate
                   OR (wydane_ubrania.data_waznosci > :currentDate AND wydane_ubrania.data_waznosci <= :twoMonthsAhead))
              AND wydane_ubrania.status = 1
            GROUP BY ubranie.nazwa_ubrania, rozmiar.nazwa_rozmiaru");

        $currentDate = $this->expiryService->getCurrentDateFormatted();
        $twoMonthsAhead = $this->expiryService->getExpiryWarningDateFormatted();
        
        $stmt->bindValue(':currentDate', $currentDate);
        $stmt->bindValue(':twoMonthsAhead', $twoMonthsAhead);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWydaneUbraniaWithDetails(): array {    
        $stmt = $this->pdo->prepare("SELECT wu.id, DATE_FORMAT(wu.data_waznosci, '%Y-%m-%d %H:%i') AS data, 
                   u.nazwa_ubrania AS nazwa_ubrania, r.nazwa_rozmiaru AS rozmiar, wu.ilosc, uz.nazwa AS wydane_przez, 
                   CONCAT(p.imie, ' ', p.nazwisko) AS wydane_dla 
            FROM wydane_ubrania wu LEFT JOIN ubranie u ON wu.id_ubrania = u.id_ubranie 
            LEFT JOIN rozmiar r ON wu.id_rozmiaru = r.id_rozmiar LEFT JOIN wydania w ON wu.id_wydania = w.id_wydania 
            LEFT JOIN pracownicy p ON w.pracownik_id = p.id_pracownik LEFT JOIN uzytkownicy uz ON w.user_id = uz.id 
            WHERE wu.data_waznosci >= :sixMonthsAgo ORDER BY nazwa_ubrania");
    
        $stmt->bindValue(':sixMonthsAgo', $this->expiryService->getHistoryStartDateFormatted());
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUbraniaById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT id_wydania, id_ubrania, id_rozmiaru, ilosc FROM wydane_ubrania WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Pobiera historię wydań dla pracownika - jedno zapytanie zamiast N+1
     * Używane w issue_history.php
     */
    public function getIssueHistoryByEmployeeId(int $pracownikId): array {
        $stmt = $this->pdo->prepare("SELECT 
                w.id_wydania,
                w.data_wydania,
                uz.nazwa AS user_name,
                wu.id,
                wu.ilosc,
                wu.data_waznosci,
                wu.status,
                u.nazwa_ubrania,
                r.nazwa_rozmiaru,
                CASE 
                    WHEN wu.data_waznosci <= :currentDate THEN 1
                    WHEN wu.data_waznosci <= :twoMonthsAhead THEN 1
                    ELSE 0
                END AS canBeReported
            FROM wydane_ubrania wu
            JOIN wydania w ON wu.id_wydania = w.id_wydania
            JOIN ubranie u ON wu.id_ubrania = u.id_ubranie
            JOIN rozmiar r ON wu.id_rozmiaru = r.id_rozmiar
            LEFT JOIN uzytkownicy uz ON w.user_id = uz.id
            WHERE w.pracownik_id = :pracownik_id
            ORDER BY w.data_wydania DESC, wu.id");
        
        $stmt->bindValue(':pracownik_id', $pracownikId, PDO::PARAM_INT);
        $stmt->bindValue(':currentDate', $this->expiryService->getCurrentDateFormatted());
        $stmt->bindValue(':twoMonthsAhead', $this->expiryService->getExpiryWarningDateFormatted());
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Pobiera wygasające/przeterminowane ubrania dla konkretnego pracownika
     * Używane w issue_clothing.php (fromRaport)
     */
    public function getExpiringClothingByEmployeeId(int $pracownikId): array {
        $stmt = $this->pdo->prepare("SELECT 
                wu.id,
                wu.ilosc,
                wu.data_waznosci,
                u.nazwa_ubrania,
                r.nazwa_rozmiaru,
                CASE 
                    WHEN wu.data_waznosci <= :currentDate THEN 'Przeterminowane'
                    WHEN wu.data_waznosci <= :twoMonthsAhead THEN 'Koniec ważności'
                    ELSE 'Brak danych'
                END AS statusText
            FROM wydane_ubrania wu
            JOIN wydania w ON wu.id_wydania = w.id_wydania
            JOIN ubranie u ON wu.id_ubrania = u.id_ubranie
            JOIN rozmiar r ON wu.id_rozmiaru = r.id_rozmiar
            WHERE w.pracownik_id = :pracownik_id
              AND wu.status = 1 
              AND (wu.data_waznosci <= :currentDate OR wu.data_waznosci <= :twoMonthsAhead)
            ORDER BY wu.data_waznosci ASC");
        
        $currentDate = $this->expiryService->getCurrentDateFormatted();
        $twoMonthsAhead = $this->expiryService->getExpiryWarningDateFormatted();
        
        $stmt->bindValue(':pracownik_id', $pracownikId, PDO::PARAM_INT);
        $stmt->bindValue(':currentDate', $currentDate);
        $stmt->bindValue(':twoMonthsAhead', $twoMonthsAhead);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Pobiera wszystkie ubrania wygasające/przeterminowane z danymi pracowników
     * Używane w raporcie 
     */
    public function getExpiringClothingWithEmployeeDetails(): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                w.id_wydania,
                w.pracownik_id,
                p.imie AS pracownik_imie,
                p.nazwisko AS pracownik_nazwisko,
                p.stanowisko AS pracownik_stanowisko,
                wu.id,
                wu.ilosc,
                wu.data_waznosci,
                u.nazwa_ubrania,
                r.nazwa_rozmiaru,
                CASE 
                    WHEN wu.data_waznosci <= :currentDate THEN 'Przeterminowane'
                    WHEN wu.data_waznosci <= :twoMonthsAhead THEN 'Koniec ważności'
                    ELSE 'Brak danych'
                END AS statusText
            FROM wydane_ubrania wu
            JOIN wydania w ON wu.id_wydania = w.id_wydania
            JOIN pracownicy p ON w.pracownik_id = p.id_pracownik
            JOIN ubranie u ON wu.id_ubrania = u.id_ubranie
            JOIN rozmiar r ON wu.id_rozmiaru = r.id_rozmiar
            WHERE wu.status = 1 
              AND (wu.data_waznosci <= :currentDate OR wu.data_waznosci <= :twoMonthsAhead)
            ORDER BY wu.data_waznosci ASC
        ");
        
        $currentDate = $this->expiryService->getCurrentDateFormatted();
        $twoMonthsAhead = $this->expiryService->getExpiryWarningDateFormatted();
        
        $stmt->bindValue(':currentDate', $currentDate);
        $stmt->bindValue(':twoMonthsAhead', $twoMonthsAhead);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}

