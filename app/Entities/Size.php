<?php
declare(strict_types=1);
namespace App\Entities;

class Size {
    private ?int $id_rozmiar = null;
    private string $nazwa_rozmiaru;

    public function __construct(string $nazwa_rozmiaru) {
        $nazwa_rozmiaru = trim($nazwa_rozmiaru);
        if ($nazwa_rozmiaru === '') {
            throw new \InvalidArgumentException('Nazwa rozmiaru nie może być pusta.');
        }
        $this->nazwa_rozmiaru = strtoupper($nazwa_rozmiaru);
    }

    public static function fromDatabase(int $id, string $nazwa_rozmiaru): self {
        $entity = new self($nazwa_rozmiaru);
        $entity->id_rozmiar = $id;
        return $entity;
    }

    public function getIdRozmiar(): ?int {
        return $this->id_rozmiar;
    }

    public function getNazwaRozmiaru(): string {
        return $this->nazwa_rozmiaru;
    }
}