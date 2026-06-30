<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/prioridad.php";

class PrioridadDAO
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaPrioridades()
    {
        $sql = "SELECT * FROM prioridades";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();
        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrioridad($id)
    {
        $sql = "SELECT * FROM prioridades WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);
        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createPrioridad(Prioridad $prioridad)
    {
        try {
            $query = "INSERT INTO prioridades (nombre, nivel) VALUES (?, ?)";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([
                $prioridad->getNombre(),
                $prioridad->getNivel()
            ]);
        } catch (PDOException $e) {
            echo "Error al crear prioridad: " . $e->getMessage();
            return false;
        }
    }

    public function updatePrioridad(Prioridad $prioridad)
    {
        try {
            $query = "UPDATE prioridades SET nombre = ?, nivel = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([
                $prioridad->getNombre(),
                $prioridad->getNivel(),
                $prioridad->getId()
            ]);
        } catch (PDOException $e) {
            echo "Error al actualizar prioridad: " . $e->getMessage();
            return false;
        }
    }

    public function deletePrioridad($id)
    {
        try {
            $query = "DELETE FROM prioridades WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            echo "Error al eliminar prioridad: " . $e->getMessage();
            return false;
        }
    }
}