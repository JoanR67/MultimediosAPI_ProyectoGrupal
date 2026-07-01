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
        // Crea conexion PDO para roles.
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaRoles()
    {
        // Lista catalogo completo ordenado por id.
        $sql = "SELECT * FROM roles ORDER BY id";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRol($id)
    {
        // Busca un rol especifico.
        $sql = "SELECT * FROM roles WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createRol(Rol $rol)
    {
        try {
            // Inserta nombre unico de rol.
            $sql = "INSERT INTO roles (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$rol->getNombre()]);
        } catch (PDOException $e) {
            // Puede fallar por nombre duplicado.
            return false;
        }
    }

    public function updateRol(Rol $rol)
    {
        try {
            // Actualiza el nombre del rol.
            $sql = "UPDATE roles SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$rol->getNombre(), $rol->getId()]);
        } catch (PDOException $e) {
            // Puede fallar por nombre duplicado.
            return false;
        }
    }

    public function deleteRol($id)
    {
        try {
            // MySQL bloquea el borrado si hay usuarios con este rol.
            $sql = "DELETE FROM roles WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            // El controlador responde 409.
            return false;
        }
    }
}
