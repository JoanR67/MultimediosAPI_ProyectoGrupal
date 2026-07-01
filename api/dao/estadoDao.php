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
        // Crea conexion PDO para estados.
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaEstados()
    {
        // Lista catalogo completo ordenado por id.
        $sql = "SELECT * FROM estados ORDER BY id";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEstado($id)
    {
        // Busca un estado especifico.
        $sql = "SELECT * FROM estados WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createEstado(Estado $estado)
    {
        try {
            // Inserta nombre unico de estado.
            $sql = "INSERT INTO estados (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$estado->getNombre()]);
        } catch (PDOException $e) {
            // Puede fallar por nombre duplicado.
            return false;
        }
    }

    public function updateEstado(Estado $estado)
    {
        try {
            // Actualiza el nombre del estado.
            $sql = "UPDATE estados SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$estado->getNombre(), $estado->getId()]);
        } catch (PDOException $e) {
            // Puede fallar por nombre duplicado.
            return false;
        }
    }

    public function deleteEstado($id)
    {
        try {
            // MySQL bloquea el borrado si hay tickets usando el estado.
            $sql = "DELETE FROM estados WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            // El controlador responde 409.
            return false;
        }
    }
}
