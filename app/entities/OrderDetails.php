<?php

class OrderDetails {
    private  $id;
    private  $zamowienie_id;
    private  $id_ubrania;
    private  $id_rozmiaru;
    private  $ilosc;
    private $iloscMin;
    private  $firma;
    private $sz_kodID;

    public function __construct($zamowienie_id = null, $id_ubrania = null, $id_rozmiaru = null, $ilosc = 0, $iloscMin = 0, $firma = '', $sz_kodID=0) {
        $this->zamowienie_id = $zamowienie_id;
        $this->id_ubrania = $id_ubrania;
        $this->id_rozmiaru = $id_rozmiaru;
        $this->ilosc = $ilosc;
        $this->iloscMin = $iloscMin;
        $this->firma = $firma;
        $this->sz_kodID = $sz_kodID;
    }

    public function getId() {
        return $this->id;
    }

    public function getZamowienieId() {
        return $this->zamowienie_id;
    }

    public function setZamowienieID($zamowienie_id) {
        $this->zamowienie_id = $zamowienie_id;
    }

    public function getIdUbrania() {
        return $this->id_ubrania;
    }

    public function setIdUbrania($id_ubrania) {
        $this->id_ubrania = $id_ubrania;
    }

    public function getIdRozmiaru() {
        return $this->id_rozmiaru;
    }

    public function setIdRozmiaru($id_rozmiaru) {
        $this->id_rozmiaru = $id_rozmiaru;
    }

    public function getIlosc() {
        return $this->ilosc;
    }

    public function setIlosc($ilosc)  {
        $this->ilosc = $ilosc;
    }

    public function getIloscMin() {
        return $this->iloscMin;
    }

    public function setIloscMin($iloscMin)  {
        $this->iloscMin = $iloscMin;
    }

    public function getFirma() {
        return $this->firma;
    }

    public function setFirma($firma) {
        $this->firma = $firma;
    }

    public function getSzKodID() {
        return $this->sz_kodID;
    }

    public function setSzKodID($sz_kodID) {
        $this->sz_kodID = $sz_kodID;
    }

}

