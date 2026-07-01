<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/rol.php";

/**
 * ============================================================
 * SECTION: DAO de roles
 * ============================================================
 *
 * Gestiona las consultas SQL de la tabla `roles`.
 */
class RolDAO
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaRoles()
    {
        $sql = "SELECT * FROM roles ORDER BY id";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRol($id)
    {
        $sql = "SELECT * FROM roles WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createRol(Rol $rol)
    {
        try {
            $sql = "INSERT INTO roles (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$rol->getNombre()]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateRol(Rol $rol)
    {
        try {
            $sql = "UPDATE roles SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$rol->getNombre(), $rol->getId()]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteRol($id)
    {
        try {
            $sql = "DELETE FROM roles WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
