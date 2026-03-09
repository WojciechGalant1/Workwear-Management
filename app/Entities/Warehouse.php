<?php
declare(strict_types=1);
namespace App\Entities;

class Warehouse {
    private ?int $id = null;
    private int $id_ubrania;
    private int $id_rozmiaru;
    private int $ilosc;
    private int $iloscMin;

    public function __construct(int $id_ubrania, int $id_rozmiaru, int $ilosc, int $iloscMin = 0) {
        if ($id_ubrania <= 0) {
            throw new \InvalidArgumentException('ID ubrania musi być większe od 0.');
        }
        if ($id_rozmiaru <= 0) {
            throw new \InvalidArgumentException('ID rozmiaru musi być większe od 0.');
        }
        if ($ilosc < 0) {
            throw new \InvalidArgumentException('Ilość nie może być ujemna.');
        }
        if ($iloscMin < 0) {
            throw new \InvalidArgumentException('Ilość minimalna nie może być ujemna.');
        }
        $this->id_ubrania = $id_ubrania;
        $this->id_rozmiaru = $id_rozmiaru;
        $this->ilosc = $ilosc;
        $this->iloscMin = $iloscMin;
    }

    public static function fromDatabase(int $id, int $id_ubrania, int $id_rozmiaru, int $ilosc, int $iloscMin): self {
        $entity = new self($id_ubrania, $id_rozmiaru, $ilosc, $iloscMin);
        $entity->id = $id;
        return $entity;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getIdUbrania(): int {
        return $this->id_ubrania;
    }

    public function getIdRozmiaru(): int {
        return $this->id_rozmiaru;
    }

    public function getIlosc(): int {
        return $this->ilosc;
    }

    public function getIloscMin(): int {
        return $this->iloscMin;
    }
}