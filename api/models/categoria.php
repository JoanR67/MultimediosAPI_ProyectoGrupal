<?php

/**
 * ============================================================
 * SECTION: Modelo Categoria
 * ============================================================
 *
 * Representa la tabla `categorias`.
 */
class Categoria
{
    private $id;
    private $nombre;

    /**
     * Crea una categoria con valores opcionales.
     */
    public function __construct($id = null, $nombre = null)
    {
        // Guarda los datos iniciales de la categoria.
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getId()
    {
        // Devuelve el id de la categoria.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado para update/delete.
        $this->id = $id;
    }

    public function getNombre()
    {
        // Devuelve el nombre de la categoria.
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        // Guarda el nombre de la categoria.
        $this->nombre = $nombre;
    }
}
