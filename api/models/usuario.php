<?php

/**
 * ============================================================
 * SECTION: Modelo Usuario
 * ============================================================
 *
 * Representa la tabla `usuarios`.
 */
class Usuario
{
    private $id;
    private $nombre;
    private $email;
    private $password;
    private $rol_id;

    /**
     * Crea un usuario con valores opcionales.
     */
    public function __construct($id = null, $nombre = null, $email = null, $password = null, $rol_id = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->rol_id = $rol_id;
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

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRolId()
    {
        return $this->rol_id;
    }

    public function setRolId($rol_id)
    {
        $this->rol_id = $rol_id;
    }
}
