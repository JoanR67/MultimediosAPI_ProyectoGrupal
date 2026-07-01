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
        // Guarda los datos iniciales del rol.
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getId()
    {
        // Devuelve el id del rol.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado para update/delete.
        $this->id = $id;
    }

    public function getNombre()
    {
        // Devuelve el nombre del rol.
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        // Guarda el nombre del rol.
        $this->nombre = $nombre;
    }
}
