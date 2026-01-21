<?php

class Clothing {
    private $id_ubranie;
    private $nazwa_ubrania;

    public function __construct($nazwa_ubrania = '') {
        $this->nazwa_ubrania = $nazwa_ubrania;
    }

    public function getIdUbranie() {
        return $this->id_ubranie;
    }

    public function setIdUbranie($id) {
        $this->id_ubranie = $id;
    }

    public function getNazwaUbrania() {
        return $this->nazwa_ubrania;
    }

    public function setNazwaUbrania($nazwa_ubrania) {
        $this->nazwa_ubrania = $nazwa_ubrania;
    }

}
?>

