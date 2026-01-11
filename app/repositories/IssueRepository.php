<?php
include_once __DIR__ . '/BaseRepository.php';
include_once __DIR__ . '/../models/Issue.php';

class IssueRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }
    
    public function create(Issue $wydania) {
        $stmt = $this->pdo->prepare("INSERT INTO wydania (pracownik_id, user_id, data_wydania) VALUES (:pracownik_id, :user_id, :data_wydania)");
        $stmt->bindValue(':pracownik_id', $wydania->getIdPracownik());
        $stmt->bindValue(':user_id', $wydania->getUserId());
        $stmt->bindValue(':data_wydania', $wydania->getDataWydania()->format('Y-m-d H:i:s'));
        
        try {
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        } 
    }

    public function getWydaniaByPracownikId($pracownikId) {
        $stmt = $this->pdo->prepare("SELECT w.id_wydania, w.pracownik_id, w.user_id, w.data_wydania, u.nazwa AS user_name
                               FROM wydania w LEFT JOIN uzytkownicy u ON w.user_id = u.id 
                               WHERE w.pracownik_id = :pracownik_id");
        $stmt->bindValue(':pracownik_id', $pracownikId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWydania() {
        $stmt = $this->pdo->prepare("SELECT w.id_wydania, w.pracownik_id, w.data_wydania, p.imie, p.nazwisko, p.stanowisko 
            FROM wydania w LEFT JOIN pracownicy p ON w.pracownik_id = p.id_pracownik");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailedWydania() {
        $stmt = $this->pdo->prepare("SELECT wu.id AS wydane_ubranie_id, wu.ilosc, wu.data_waznosci, u.nazwa_ubrania, r.nazwa_rozmiaru, wydania.data_wydania, wydania.pracownik_id,
         p.imie AS pracownik_imie, p.nazwisko AS pracownik_nazwisko, wydania.user_id, uzytkownicy.nazwa AS wydane_przez
            FROM wydane_ubrania wu JOIN ubranie u ON wu.id_ubrania = u.id_ubranie JOIN rozmiar r ON wu.id_rozmiaru = r.id_rozmiar JOIN wydania ON wu.id_wydania = wydania.id_wydania
            JOIN pracownicy p ON wydania.pracownik_id = p.id_pracownik JOIN uzytkownicy ON wydania.user_id = uzytkownicy.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function deleteWydanie($id_wydania) {
        $stmt = $this->pdo->prepare("DELETE FROM wydania WHERE id_wydania = :id_wydania");
        $stmt->bindValue(':id_wydania', $id_wydania, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
}
?>

