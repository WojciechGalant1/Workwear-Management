<?php

class Warehouse
{
    private $id;
    private $id_ubrania;
    private $id_rozmiaru;
    private $ilosc;
    private $iloscMin;

    public function __construct($id_ubrania = null, $id_rozmiaru = null, $ilosc = 0, $iloscMin = 0)
    {
        $this->id_ubrania = $id_ubrania;
        $this->id_rozmiaru = $id_rozmiaru;
        $this->ilosc = $ilosc;
        $this->iloscMin = $iloscMin;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdUbrania()
    {
        return $this->id_ubrania;
    }

    public function setIdUbrania($id_ubrania)
    {
        $this->id_ubrania = $id_ubrania;
    }

    public function getIdRozmiaru()
    {
        return $this->id_rozmiaru;
    }

    public function setIdRozmiaru($id_rozmiaru)
    {
        $this->id_rozmiaru = $id_rozmiaru;
    }

    public function getIlosc()
    {
        return $this->ilosc;
    }

    public function setIlosc($ilosc)
    {
        $this->ilosc = $ilosc;
    }

    public function getIloscMin()
    {
        return $this->iloscMin;
    }

    public function setIloscMin($iloscMin)
    {
        $this->iloscMin = $iloscMin;
    }
}

