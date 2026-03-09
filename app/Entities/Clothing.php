<?php
declare(strict_types=1);
namespace App\Entities;

class Clothing {
    private ?int $id_ubranie = null;
    private string $nazwa_ubrania;

    public function __construct(string $nazwa_ubrania) {
        $nazwa_ubrania = trim($nazwa_ubrania);
        if ($nazwa_ubrania === '') {
            throw new \InvalidArgumentException('Nazwa ubrania nie może być pusta.');
        }
        $this->nazwa_ubrania = $nazwa_ubrania;
    }

    public static function fromDatabase(int $id, string $nazwa_ubrania): self {
        $entity = new self($nazwa_ubrania);
        $entity->id_ubranie = $id;
        return $entity;
    }

    public function getIdUbranie(): ?int {
        return $this->id_ubranie;
    }

    public function getNazwaUbrania(): string {
        return $this->nazwa_ubrania;
    }
}
