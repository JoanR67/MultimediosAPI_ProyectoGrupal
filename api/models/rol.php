<?php

/**
 * ============================================================
 * SECTION: Modelo Rol
 * ============================================================
 *
 * Representa la tabla `roles`.
 */
class Rol
{
    private $id;
    private $nombre;

    /**
     * Crea un rol con valores opcionales.
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
