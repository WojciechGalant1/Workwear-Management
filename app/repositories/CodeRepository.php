<?php
include_once __DIR__ . '/BaseRepository.php';
include_once __DIR__ . '/../entities/Code.php';

class CodeRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }
    
    public function create(Code $kod): string|false {
        $stmt = $this->pdo->prepare("INSERT INTO kod (kod_nazwa, ubranieID, rozmiarID, status) VALUES (:kod_nazwa, :ubranieID, :rozmiarID, :status)");
        $stmt->bindValue(':kod_nazwa', $kod->getNazwaKod());
        $stmt->bindValue(':ubranieID', $kod->getUbranieID());
        $stmt->bindValue(':rozmiarID', $kod->getRozmiarID());
        $stmt->bindValue(':status', $kod->getStatus());

        return $stmt->execute() ? $this->pdo->lastInsertId() : false;
    }

    public function findByNazwa(string $kod_nazwa): ?array {
        $stmt = $this->pdo->prepare("SELECT k.kod_nazwa, u.nazwa_ubrania, r.nazwa_rozmiaru, k.ubranieID, k.rozmiarID FROM kod k
         JOIN ubranie u ON k.ubranieID = u.id_ubranie JOIN rozmiar r ON k.rozmiarID = r.id_rozmiar WHERE k.kod_nazwa = :kod_nazwa");
        $stmt->bindValue(':kod_nazwa', $kod_nazwa);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($row) {
            return [
                'id_ubrania' => $row['ubranieID'],
                'nazwa_ubrania' => $row['nazwa_ubrania'],
                'id_rozmiar' => $row['rozmiarID'],
                'nazwa_rozmiaru' => $row['nazwa_rozmiaru'],
            ];
        }
        return null;
    }

    public function findKodByNazwa(string $kod_nazwa): Code|false {
        $stmt = $this->pdo->prepare('SELECT id_kod FROM kod WHERE kod_nazwa = :kod_nazwa');
        $stmt->bindValue(':kod_nazwa', $kod_nazwa);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Code');
        return $stmt->fetch(); 
    }
    
}

