<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/rol.php";

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
        $sql = "SELECT * FROM roles";
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
            $query = "INSERT INTO roles (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([$rol->getNombre()]);
        } catch (PDOException $e) {
            echo "Error al crear rol: " . $e->getMessage();
            return false;
        }
    }

    public function updateRol(Rol $rol)
    {
        try {
            $query = "UPDATE roles SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([
                $rol->getNombre(),
                $rol->getId()
            ]);
        } catch (PDOException $e) {
            echo "Error al actualizar rol: " . $e->getMessage();
            return false;
        }
    }

    public function deleteRol($id)
    {
        try {
            $query = "DELETE FROM roles WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            echo "Error al eliminar rol: " . $e->getMessage();
            return false;
        }
    }
}