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
        $this->id = $id;
        $this->nombre = $nombre;
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
}
