<?php
namespace App\Repositories;

use App\Entities\Size;
use App\Repositories\BaseRepository;

use PDO;

class SizeRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }
    
    public function create(Size $rozmiar): string|false {
        $stmt = $this->pdo->prepare("INSERT INTO rozmiar (nazwa_rozmiaru) VALUES (:nazwa_rozmiaru)");
        $nazwa_rozmiaru = $rozmiar->getNazwaRozmiaru();
        $stmt->bindParam(':nazwa_rozmiaru', $nazwa_rozmiaru);
        
        $stmt->execute();
        return $this->pdo->lastInsertId(); 
    }

    public function findByName(string $nazwa): ?Size {
        $stmt = $this->pdo->prepare("SELECT id_rozmiar, nazwa_rozmiaru FROM rozmiar WHERE nazwa_rozmiaru = :nazwa_rozmiaru");
        $stmt->bindParam(':nazwa_rozmiaru', $nazwa);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $rozmiar = new Size($result['nazwa_rozmiaru']);
            $rozmiar->setIdRozmiar($result['id_rozmiar']);
            return $rozmiar;
        }
        return null;
    }

    public function firstOrCreate(Size $rozmiar): string|false {
        $rozmiar->setNazwaRozmiaru($rozmiar->getNazwaRozmiaru());
        $existing = $this->findByName($rozmiar->getNazwaRozmiaru());
        if ($existing) {
            return $existing->getIdRozmiar();
        }
        return $this->create($rozmiar);
    }

    public function searchByName(string $query): array {
        $stmt = $this->pdo->prepare('SELECT nazwa_rozmiaru AS rozmiar FROM rozmiar WHERE nazwa_rozmiaru LIKE :query LIMIT 10');
        $searchQuery = "%$query%";
        $stmt->bindParam(':query', $searchQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

