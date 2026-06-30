<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/estado.php";

class EstadoDAO
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaEstados()
    {
        $sql = "SELECT * FROM estados";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();
        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEstado($id)
    {
        $sql = "SELECT * FROM estados WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);
        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createEstado(Estado $estado)
    {
        try {
            $query = "INSERT INTO estados (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([$estado->getNombre()]);
        } catch (PDOException $e) {
            echo "Error al crear estado: " . $e->getMessage();
            return false;
        }
    }

    public function updateEstado(Estado $estado)
    {
        try {
            $query = "UPDATE estados SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([
                $estado->getNombre(),
                $estado->getId()
            ]);
        } catch (PDOException $e) {
            echo "Error al actualizar estado: " . $e->getMessage();
            return false;
        }
    }

    public function deleteEstado($id)
    {
        try {
            $query = "DELETE FROM estados WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            echo "Error al eliminar estado: " . $e->getMessage();
            return false;
        }
    }
}