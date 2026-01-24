<?php
namespace App\Entities;

class OrderDetails {
    private ?int $id = null;
    private ?int $zamowienie_id = null;
    private ?int $id_ubrania = null;
    private ?int $id_rozmiaru = null;
    private int $ilosc = 0;
    private int $iloscMin = 0;
    private string $firma = '';
    private int $sz_kodID = 0;

    public function __construct(?int $zamowienie_id = null, ?int $id_ubrania = null, ?int $id_rozmiaru = null, int $ilosc = 0, int $iloscMin = 0, string $firma = '', int $sz_kodID = 0) {
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

    public function getZamowienieId(): ?int {
        return $this->zamowienie_id;
    }

    public function setZamowienieID(?int $zamowienie_id): void {
        $this->zamowienie_id = $zamowienie_id;
    }

    public function getIdUbrania(): ?int {
        return $this->id_ubrania;
    }

    public function setIdUbrania(?int $id_ubrania): void {
        $this->id_ubrania = $id_ubrania;
    }

    public function getIdRozmiaru(): ?int {
        return $this->id_rozmiaru;
    }

    public function setIdRozmiaru(?int $id_rozmiaru): void {
        $this->id_rozmiaru = $id_rozmiaru;
    }

    public function getIlosc(): int {
        return $this->ilosc;
    }

    public function setIlosc(int $ilosc): void {
        $this->ilosc = $ilosc;
    }

    public function getIloscMin(): int {
        return $this->iloscMin;
    }

    public function setIloscMin(int $iloscMin): void {
        $this->iloscMin = $iloscMin;
    }

    public function getFirma(): string {
        return $this->firma;
    }

    public function setFirma(string $firma): void {
        $this->firma = $firma;
    }

    public function getSzKodID(): int {
        return $this->sz_kodID;
    }

    public function setSzKodID(int $sz_kodID): void {
        $this->sz_kodID = $sz_kodID;
    }
}