<?php
declare(strict_types=1);
namespace App\Entities;

class Code {
    private ?int $id_kod = null;
    private string $kod_nazwa;
    private int $ubranieID;
    private int $rozmiarID;
    private int $status;

    public function __construct(string $kod_nazwa, int $ubranieID, int $rozmiarID, int $status) {
        if (trim($kod_nazwa) === '') {
            throw new \InvalidArgumentException('Kod nazwa nie może być pusty.');
        }
        if ($ubranieID <= 0) {
            throw new \InvalidArgumentException('ID ubrania musi być większe od 0.');
        }
        if ($rozmiarID <= 0) {
            throw new \InvalidArgumentException('ID rozmiaru musi być większe od 0.');
        }
        $this->kod_nazwa = trim($kod_nazwa);
        $this->ubranieID = $ubranieID;
        $this->rozmiarID = $rozmiarID;
        $this->status = $status;
    }

    public static function fromDatabase(int $id, string $kod_nazwa, int $ubranieID, int $rozmiarID, int $status): self {
        $entity = new self($kod_nazwa, $ubranieID, $rozmiarID, $status);
        $entity->id_kod = $id;
        return $entity;
    }

    public function getIdKod(): ?int {
        return $this->id_kod;
    }

    public function getNazwaKod(): string {
        return $this->kod_nazwa;
    }

    public function getUbranieID(): int {
        return $this->ubranieID;
    }

    public function getRozmiarID(): int {
        return $this->rozmiarID;
    }

    public function getStatus(): int {
        return $this->status;
    }
}
