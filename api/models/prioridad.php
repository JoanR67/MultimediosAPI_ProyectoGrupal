<?php

/**
 * ============================================================
 * SECTION: Modelo Prioridad
 * ============================================================
 *
 * Representa la tabla `prioridades`.
 */
class Prioridad
{
    private $id;
    private $nombre;
    private $nivel;

    /**
     * Crea una prioridad con valores opcionales.
     */
    public function __construct($id = null, $nombre = null, $nivel = null)
    {
        // Guarda los datos iniciales de la prioridad.
        $this->id = $id;
        $this->nombre = $nombre;
        $this->nivel = $nivel;
    }

    public function getId()
    {
        // Devuelve el id de la prioridad.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado para update/delete.
        $this->id = $id;
    }

    public function getNombre()
    {
        // Devuelve el nombre de la prioridad.
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        // Guarda el nombre de la prioridad.
        $this->nombre = $nombre;
    }

    public function getNivel()
    {
        // Devuelve el nivel usado para ordenar prioridades.
        return $this->nivel;
    }

    public function setNivel($nivel)
    {
        // Guarda el nivel de importancia.
        $this->nivel = $nivel;
    }
}
