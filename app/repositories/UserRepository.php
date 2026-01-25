<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Repositories\BaseRepository;
use PDO;

class UserRepository extends BaseRepository{

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }
  
    public function getUserById(int $userId): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM uzytkownicy WHERE id = :id");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
