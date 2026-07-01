<?php

/**
 * ============================================================
 * SECTION: Modelo Estado
 * ============================================================
 *
 * Representa la tabla `estados`.
 */
class Estado
{
    private $id;
    private $nombre;

    /**
     * Crea un estado con valores opcionales.
     */
    public function __construct($id = null, $nombre = null)
    {
        // Guarda los datos iniciales del estado.
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getId()
    {
        // Devuelve el id del estado.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado para update/delete.
        $this->id = $id;
    }

    public function getNombre()
    {
        // Devuelve el nombre del estado.
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        // Guarda el nombre del estado.
        $this->nombre = $nombre;
    }
}
