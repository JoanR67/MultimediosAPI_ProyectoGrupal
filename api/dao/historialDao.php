<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/historial.php";

/**
 * ============================================================
 * SECTION: DAO de historial
 * ============================================================
 *
 * Centraliza las consultas SQL de la tabla `historial`.
 */
class HistorialDAO
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    /**
     * Lista registros de historial. Si se envia ticket_id, filtra por ticket.
     */
    public function listaHistorial($ticket_id = null)
    {
        $sql = "SELECT h.*, t.titulo AS ticket, u.nombre AS usuario
                FROM historial h
                INNER JOIN tickets t ON h.ticket_id = t.id
                INNER JOIN usuarios u ON h.usuario_id = u.id";

        if ($ticket_id != null) {
            $sql .= " WHERE h.ticket_id = ?";
        }

        $sql .= " ORDER BY h.id";

        $preparado = $this->conexion->prepare($sql);
        $ticket_id != null ? $preparado->execute([$ticket_id]) : $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un registro de historial por id.
     */
    public function getHistorial($id)
    {
        $sql = "SELECT * FROM historial WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un registro manual de historial y devuelve su id.
     */
    public function createHistorial(Historial $historial)
    {
        try {
            $sql = "INSERT INTO historial (ticket_id, usuario_id, accion, valor_anterior, valor_nuevo)
                    VALUES (?, ?, ?, ?, ?)";
            $preparado = $this->conexion->prepare($sql);
            $preparado->execute([
                $historial->getTicketId(),
                $historial->getUsuarioId(),
                $historial->getAccion(),
                $historial->getValorAnterior(),
                $historial->getValorNuevo()
            ]);

            return (int) $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza un registro de historial.
     */
    public function updateHistorial(Historial $historial)
    {
        try {
            $sql = "UPDATE historial
                    SET ticket_id = ?, usuario_id = ?, accion = ?, valor_anterior = ?, valor_nuevo = ?
                    WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([
                $historial->getTicketId(),
                $historial->getUsuarioId(),
                $historial->getAccion(),
                $historial->getValorAnterior(),
                $historial->getValorNuevo(),
                $historial->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina un registro de historial por id.
     */
    public function deleteHistorial($id)
    {
        try {
            $sql = "DELETE FROM historial WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
