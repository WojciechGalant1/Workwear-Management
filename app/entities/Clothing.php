<?php
class Clothing {
    private ?int $id_ubranie = null;
    private string $nazwa_ubrania = '';

    public function __construct(string $nazwa_ubrania = '') {
        $this->nazwa_ubrania = $nazwa_ubrania;
    }

    public function getIdUbranie(): ?int {
        return $this->id_ubranie;
    }

    public function setIdUbranie(?int $id): void {
        $this->id_ubranie = $id;
    }

    public function getNazwaUbrania(): string {
        return $this->nazwa_ubrania;
    }

    public function setNazwaUbrania(string $nazwa_ubrania): void {
        $this->nazwa_ubrania = $nazwa_ubrania;
    }
}
