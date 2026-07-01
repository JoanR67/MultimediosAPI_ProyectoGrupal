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
        // Crea conexion PDO para historial.
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    /**
     * Lista registros de historial. Si se envia ticket_id, filtra por ticket.
     */
    public function listaHistorial($ticket_id = null)
    {
        // JOIN agrega informacion legible del ticket y usuario.
        $sql = "SELECT h.*, t.titulo AS ticket, u.nombre AS usuario
                FROM historial h
                INNER JOIN tickets t ON h.ticket_id = t.id
                INNER JOIN usuarios u ON h.usuario_id = u.id";

        if ($ticket_id != null) {
            // Filtro opcional para ver solo eventos de un ticket.
            $sql .= " WHERE h.ticket_id = ?";
        }

        $sql .= " ORDER BY h.id";

        // Prepara la consulta para usar parametros cuando hay filtro.
        $preparado = $this->conexion->prepare($sql);
        $ticket_id != null ? $preparado->execute([$ticket_id]) : $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un registro de historial por id.
     */
    public function getHistorial($id)
    {
        // Busca un registro historico por id.
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
            // Inserta accion, valor anterior y nuevo para trazabilidad.
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

            // Devuelve el id generado.
            return (int) $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            // Puede fallar si ticket_id o usuario_id no existen.
            return false;
        }
    }

    /**
     * Actualiza un registro de historial.
     */
    public function updateHistorial(Historial $historial)
    {
        try {
            // Permite corregir un registro historico manual.
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
            // El controlador decide el mensaje HTTP.
            return false;
        }
    }

    /**
     * Elimina un registro de historial por id.
     */
    public function deleteHistorial($id)
    {
        try {
            // Elimina un registro historico por id.
            $sql = "DELETE FROM historial WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            // False evita imprimir errores internos de MySQL.
            return false;
        }
    }
}
