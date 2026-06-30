<?php

class Prioridad
{
    private $id;
    private $nombre;
    private $nivel;

    public function __construct($id = null, $nombre = null, $nivel = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->nivel = $nivel;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getNivel()
    {
        return $this->nivel;
    }

    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
    }
}