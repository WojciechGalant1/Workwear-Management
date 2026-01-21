<?php

class Employee {
    private $id_pracownik;
    private $imie;
    private $nazwisko;
    private $stanowisko;
    private $status;

    public function __construct($imie = '', $nazwisko = '', $stanowisko = '', $status = 1) {
        $this->imie = $imie;
        $this->nazwisko = $nazwisko;
        $this->stanowisko = $stanowisko;
        $this->status = $status;
    }

    public function getIdPracownik() {
        return $this->id_pracownik;
    }

    public function getImie() {
        return $this->imie;
    }

    public function setImie($imie) {
        $this->imie = $imie;
    }

    public function getNazwisko() {
        return $this->nazwisko;
    }

    public function setNazwisko($nazwisko) {
        $this->nazwisko = $nazwisko;
    }

    public function getStanowisko() {
        return $this->stanowisko;
    }

    public function setStanowisko($stanowisko) {
        $this->stanowisko = $stanowisko;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
}
?>

