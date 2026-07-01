<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/estado.php";

/**
 * ============================================================
 * SECTION: DAO de estados
 * ============================================================
 *
 * Gestiona las consultas SQL de la tabla `estados`.
 */
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
        $sql = "SELECT * FROM estados ORDER BY id";
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
            $sql = "INSERT INTO estados (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$estado->getNombre()]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateEstado(Estado $estado)
    {
        try {
            $sql = "UPDATE estados SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$estado->getNombre(), $estado->getId()]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteEstado($id)
    {
        try {
            $sql = "DELETE FROM estados WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
