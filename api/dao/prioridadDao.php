<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/prioridad.php";

/**
 * ============================================================
 * SECTION: DAO de prioridades
 * ============================================================
 *
 * Gestiona las consultas SQL de la tabla `prioridades`.
 */
class PrioridadDAO
{
    private $conexion;

    public function __construct()
    {
        // Crea conexion PDO para prioridades.
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaPrioridades()
    {
        // Ordena por nivel para ver primero las prioridades menores.
        $sql = "SELECT * FROM prioridades ORDER BY nivel, id";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrioridad($id)
    {
        // Busca una prioridad especifica.
        $sql = "SELECT * FROM prioridades WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createPrioridad(Prioridad $prioridad)
    {
        try {
            // Inserta nombre y nivel de prioridad.
            $sql = "INSERT INTO prioridades (nombre, nivel) VALUES (?, ?)";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$prioridad->getNombre(), $prioridad->getNivel()]);
        } catch (PDOException $e) {
            // Puede fallar por nombre duplicado.
            return false;
        }
    }

    public function updatePrioridad(Prioridad $prioridad)
    {
        try {
            // Actualiza nombre y nivel.
            $sql = "UPDATE prioridades SET nombre = ?, nivel = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([
                $prioridad->getNombre(),
                $prioridad->getNivel(),
                $prioridad->getId()
            ]);
        } catch (PDOException $e) {
            // Puede fallar por nombre duplicado.
            return false;
        }
    }

    public function deletePrioridad($id)
    {
        try {
            // MySQL bloquea el borrado si hay tickets usando la prioridad.
            $sql = "DELETE FROM prioridades WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            // El controlador responde 409.
            return false;
        }
    }
}
