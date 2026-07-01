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
        // Guarda los datos iniciales del usuario.
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->rol_id = $rol_id;
    }

    public function getId()
    {
        // Devuelve el id del usuario.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado para update/delete.
        $this->id = $id;
    }

    public function getNombre()
    {
        // Devuelve el nombre del usuario.
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        // Guarda el nombre recibido.
        $this->nombre = $nombre;
    }

    public function getEmail()
    {
        // Devuelve el correo del usuario.
        return $this->email;
    }

    public function setEmail($email)
    {
        // Guarda el correo normalizado.
        $this->email = $email;
    }

    public function getPassword()
    {
        // Devuelve el hash de la contraseña.
        return $this->password;
    }

    public function setPassword($password)
    {
        // Guarda la contraseña ya hasheada.
        $this->password = $password;
    }

    public function getRolId()
    {
        // Devuelve el rol del usuario.
        return $this->rol_id;
    }

    public function setRolId($rol_id)
    {
        // Guarda el rol asignado al usuario.
        $this->rol_id = $rol_id;
    }
}
