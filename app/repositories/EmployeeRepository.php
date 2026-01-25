<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Entities\Employee;
use App\Repositories\BaseRepository;

use PDO;

class EmployeeRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }
    
    public function create(Employee $pracownik): bool {
        $stmt = $this->pdo->prepare("INSERT INTO pracownicy (imie, nazwisko, stanowisko, status) VALUES (:imie, :nazwisko, :stanowisko, :status)");
        $imie = $pracownik->getImie();
        $nazwisko = $pracownik->getNazwisko();
        $stanowisko = $pracownik->getStanowisko();
        $status = $pracownik->getStatus();

        $stmt->bindParam(':imie', $imie);
        $stmt->bindParam(':nazwisko', $nazwisko);
        $stmt->bindParam(':stanowisko', $stanowisko);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function update(int $id, string $imie, string $nazwisko, string $stanowisko, int $status): bool {
        $stmt = $this->pdo->prepare("UPDATE pracownicy SET imie = :imie, nazwisko = :nazwisko, stanowisko = :stanowisko, status = :status WHERE id_pracownik = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':imie', $imie);
        $stmt->bindParam(':nazwisko', $nazwisko);
        $stmt->bindParam(':stanowisko', $stanowisko);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }
//todo: add pagination
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM pracownicy");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM pracownicy WHERE id_pracownik = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchByName(string $query): array {
        $stmt = $this->pdo->prepare('SELECT id_pracownik, imie, nazwisko, stanowisko, status FROM pracownicy WHERE CONCAT(imie, " ", nazwisko) LIKE :query AND status = 1  ORDER BY nazwisko, imie LIMIT 10');
        $searchQuery = "%$query%";
        $stmt->bindParam(':query', $searchQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

