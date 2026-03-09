<?php
declare(strict_types=1);
namespace App\Entities;

class OrderDetails {
    private ?int $id = null;
    private int $zamowienie_id;
    private int $id_ubrania;
    private int $id_rozmiaru;
    private int $ilosc;
    private int $iloscMin;
    private string $firma;
    private int $sz_kodID;

    public function __construct(int $zamowienie_id, int $id_ubrania, int $id_rozmiaru, int $ilosc, int $iloscMin, string $firma, int $sz_kodID) {
        if ($zamowienie_id <= 0) {
            throw new \InvalidArgumentException('ID zamówienia musi być większe od 0.');
        }
        if ($id_ubrania <= 0) {
            throw new \InvalidArgumentException('ID ubrania musi być większe od 0.');
        }
        if ($id_rozmiaru <= 0) {
            throw new \InvalidArgumentException('ID rozmiaru musi być większe od 0.');
        }
        if ($ilosc === 0) {
            throw new \InvalidArgumentException('Ilość nie może wynosić 0.');
        }
        $this->zamowienie_id = $zamowienie_id;
        $this->id_ubrania = $id_ubrania;
        $this->id_rozmiaru = $id_rozmiaru;
        $this->ilosc = $ilosc;
        $this->iloscMin = $iloscMin;
        $this->firma = $firma;
        $this->sz_kodID = $sz_kodID;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getZamowienieId(): int {
        return $this->zamowienie_id;
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

    public function getFirma(): string {
        return $this->firma;
    }

    public function getSzKodID(): int {
        return $this->sz_kodID;
    }
}