<?php

class Size {
    private  $id_rozmiar;
    private  $nazwa_rozmiaru;

    public function __construct($nazwa_rozmiaru) {
        $this->nazwa_rozmiaru = strtoupper($nazwa_rozmiaru);
    }

    public function getIdRozmiar() {
        return $this->id_rozmiar;
    }

    public function setIdRozmiar($id_rozmiar){
        $this->id_rozmiar = $id_rozmiar;
    }

    public function getNazwaRozmiaru() {
        return $this->nazwa_rozmiaru;
    }

    public function setNazwaRozmiaru( $nazwa_rozmiaru) {
        $this->nazwa_rozmiaru = strtoupper($nazwa_rozmiaru);
    }
}
?>

