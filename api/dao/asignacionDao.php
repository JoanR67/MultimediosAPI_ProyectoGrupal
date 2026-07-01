<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/asignacion.php";

/**
 * ============================================================
 * SECTION: DAO de asignaciones
 * ============================================================
 *
 * Centraliza las consultas SQL de la tabla `asignaciones`.
 */
class AsignacionDAO
{
    private $conexion;

    public function __construct()
    {
        // Abre conexion compartida por todas las consultas de asignacion.
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    /**
     * Lista asignaciones. Si se envia ticket_id, filtra por ticket.
     */
    public function listaAsignaciones($ticket_id = null)
    {
        // JOIN agrega nombres utiles para mostrar en la respuesta.
        $sql = "SELECT a.*, t.titulo AS ticket, u.nombre AS tecnico, admin.nombre AS asignador
                FROM asignaciones a
                INNER JOIN tickets t ON a.ticket_id = t.id
                INNER JOIN usuarios u ON a.tecnico_id = u.id
                INNER JOIN usuarios admin ON a.asignado_por = admin.id";

        if ($ticket_id != null) {
            // Filtro opcional para ver asignaciones de un ticket especifico.
            $sql .= " WHERE a.ticket_id = ?";
        }

        $sql .= " ORDER BY a.id";

        // Se prepara aunque no haya filtro para mantener un estilo uniforme.
        $preparado = $this->conexion->prepare($sql);
        $ticket_id != null ? $preparado->execute([$ticket_id]) : $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una asignacion por su id.
     */
    public function getAsignacion($id)
    {
        // Busca una asignacion puntual.
        $sql = "SELECT * FROM asignaciones WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una asignacion y actualiza el tecnico del ticket.
     */
    public function createAsignacion(Asignacion $asignacion)
    {
        try {
            // Valida que el ticket exista antes de iniciar la transaccion.
            $ticket = $this->getTicket($asignacion->getTicketId());
            if (!$ticket) {
                return false;
            }

            // Transaccion: asignacion, ticket e historial deben quedar sincronizados.
            $this->conexion->beginTransaction();

            // Crea la fila de asignacion.
            $sql = "INSERT INTO asignaciones (ticket_id, tecnico_id, asignado_por) VALUES (?, ?, ?)";
            $preparado = $this->conexion->prepare($sql);
            $preparado->execute([
                $asignacion->getTicketId(),
                $asignacion->getTecnicoId(),
                $asignacion->getAsignadoPor()
            ]);

            $id = (int) $this->conexion->lastInsertId();
            // Sincroniza tecnico actual del ticket.
            $this->actualizarTecnicoTicket($asignacion->getTicketId(), $asignacion->getTecnicoId());
            // Registra trazabilidad automatica del cambio.
            $this->registrarHistorial(
                $asignacion->getTicketId(),
                $asignacion->getAsignadoPor(),
                "Asignacion de tecnico",
                $ticket["tecnico_id"],
                $asignacion->getTecnicoId()
            );

            // Confirma todos los cambios de la transaccion.
            $this->conexion->commit();

            return $id;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                // Revierte si fallo cualquier paso de la asignacion.
                $this->conexion->rollBack();
            }

            return false;
        }
    }

    /**
     * Actualiza una asignacion existente y sincroniza el ticket.
     */
    public function updateAsignacion(Asignacion $asignacion)
    {
        try {
            // Guarda datos anteriores para registrar historial.
            $actual = $this->getAsignacion($asignacion->getId());
            if (!$actual) {
                return false;
            }

            // Confirma que el ticket nuevo exista.
            $ticket = $this->getTicket($asignacion->getTicketId());
            if (!$ticket) {
                return false;
            }

            // La actualizacion tambien sincroniza ticket e historial.
            $this->conexion->beginTransaction();

            // Actualiza los datos principales de la asignacion.
            $sql = "UPDATE asignaciones
                    SET ticket_id = ?, tecnico_id = ?, asignado_por = ?
                    WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);
            $resultado = $preparado->execute([
                $asignacion->getTicketId(),
                $asignacion->getTecnicoId(),
                $asignacion->getAsignadoPor(),
                $asignacion->getId()
            ]);

            // Refleja el tecnico asignado en la tabla tickets.
            $this->actualizarTecnicoTicket($asignacion->getTicketId(), $asignacion->getTecnicoId());
            // Guarda el cambio para auditoria.
            $this->registrarHistorial(
                $asignacion->getTicketId(),
                $asignacion->getAsignadoPor(),
                "Actualizacion de asignacion",
                $actual["tecnico_id"],
                $asignacion->getTecnicoId()
            );

            $this->conexion->commit();

            return $resultado;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                // Evita dejar cambios parciales.
                $this->conexion->rollBack();
            }

            return false;
        }
    }

    /**
     * Elimina una asignacion por id.
     */
    public function deleteAsignacion($id)
    {
        try {
            // Obtiene la asignacion antes de borrarla para poder escribir historial.
            $asignacion = $this->getAsignacion($id);
            if (!$asignacion) {
                return false;
            }

            // Borra y registra historial como una unidad.
            $this->conexion->beginTransaction();

            // Elimina el registro de asignacion.
            $sql = "DELETE FROM asignaciones WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);
            $resultado = $preparado->execute([$id]);

            // Registra que la asignacion fue eliminada.
            $this->registrarHistorial(
                $asignacion["ticket_id"],
                $asignacion["asignado_por"],
                "Eliminacion de asignacion",
                $asignacion["tecnico_id"],
                null
            );

            $this->conexion->commit();

            return $resultado;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                // Revierte si falla el DELETE o el historial.
                $this->conexion->rollBack();
            }

            return false;
        }
    }

    /**
     * Busca un ticket para validar que exista antes de asignarlo.
     */
    private function getTicket($ticket_id)
    {
        // Consulta auxiliar para validar existencia del ticket.
        $sql = "SELECT * FROM tickets WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$ticket_id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Coloca el tecnico en el ticket y lo marca como asignado.
     */
    private function actualizarTecnicoTicket($ticket_id, $tecnico_id)
    {
        // Estado 2 corresponde a "Asignado" en los datos iniciales.
        $sql = "UPDATE tickets SET tecnico_id = ?, estado_id = 2 WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);

        return $preparado->execute([$tecnico_id, $ticket_id]);
    }

    /**
     * Guarda una accion automatica en la tabla historial.
     */
    private function registrarHistorial($ticket_id, $usuario_id, $accion, $valor_anterior, $valor_nuevo)
    {
        // Inserta una fila de trazabilidad para cambios de asignacion.
        $sql = "INSERT INTO historial (ticket_id, usuario_id, accion, valor_anterior, valor_nuevo)
                VALUES (?, ?, ?, ?, ?)";
        $preparado = $this->conexion->prepare($sql);

        return $preparado->execute([
            $ticket_id,
            $usuario_id,
            $accion,
            $valor_anterior,
            $valor_nuevo
        ]);
    }
}
