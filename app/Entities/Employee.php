<?php
declare(strict_types=1);
namespace App\Entities;

class Employee {
    private ?int $id_pracownik = null;
    private string $imie;
    private string $nazwisko;
    private string $stanowisko;
    private int $status;

    public function __construct(string $imie, string $nazwisko, string $stanowisko, int $status = 1) {
        $imie = trim($imie);
        $nazwisko = trim($nazwisko);
        if ($imie === '') {
            throw new \InvalidArgumentException('Imię pracownika nie może być puste.');
        }
        if ($nazwisko === '') {
            throw new \InvalidArgumentException('Nazwisko pracownika nie może być puste.');
        }
        if (!in_array($status, [0, 1], true)) {
            throw new \InvalidArgumentException('Status pracownika musi wynosić 0 lub 1.');
        }
        $this->imie = $imie;
        $this->nazwisko = $nazwisko;
        $this->stanowisko = trim($stanowisko);
        $this->status = $status;
    }

    public static function fromDatabase(int $id, string $imie, string $nazwisko, string $stanowisko, int $status): self {
        $entity = new self($imie, $nazwisko, $stanowisko, $status);
        $entity->id_pracownik = $id;
        return $entity;
    }

    public function getIdPracownik(): ?int {
        return $this->id_pracownik;
    }

    public function getImie(): string {
        return $this->imie;
    }

    public function getNazwisko(): string {
        return $this->nazwisko;
    }

    public function getStanowisko(): string {
        return $this->stanowisko;
    }

    public function getStatus(): int {
        return $this->status;
    }

    /**
     * Aktualizuje dane pracownika
     */
    public function update(string $imie, string $nazwisko, string $stanowisko, int $status): void {
        $imie = trim($imie);
        $nazwisko = trim($nazwisko);
        if ($imie === '' || $nazwisko === '') {
            throw new \InvalidArgumentException('Imię i nazwisko nie mogą być puste.');
        }
        if (!in_array($status, [0, 1], true)) {
            throw new \InvalidArgumentException('Status pracownika musi wynosić 0 lub 1.');
        }
        $this->imie = $imie;
        $this->nazwisko = $nazwisko;
        $this->stanowisko = trim($stanowisko);
        $this->status = $status;
    }

    public function getFullName(): string {
        return $this->imie . ' ' . $this->nazwisko;
    }
}