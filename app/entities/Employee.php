<?php

class Employee {
    private ?int $id_pracownik = null;
    private string $imie = '';
    private string $nazwisko = '';
    private string $stanowisko = '';
    private int $status = 1;

    public function __construct(string $imie = '', string $nazwisko = '', string $stanowisko = '', int $status = 1) {
        $this->imie = $imie;
        $this->nazwisko = $nazwisko;
        $this->stanowisko = $stanowisko;
        $this->status = $status;
    }

    public function getIdPracownik(): ?int {
        return $this->id_pracownik;
    }

    public function getImie(): string {
        return $this->imie;
    }

    public function setImie(string $imie): void {
        $this->imie = $imie;
    }

    public function getNazwisko(): string {
        return $this->nazwisko;
    }

    public function setNazwisko(string $nazwisko): void {
        $this->nazwisko = $nazwisko;
    }

    public function getStanowisko(): string {
        return $this->stanowisko;
    }

    public function setStanowisko(string $stanowisko): void {
        $this->stanowisko = $stanowisko;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status): void {
        $this->status = $status;
    }
}


