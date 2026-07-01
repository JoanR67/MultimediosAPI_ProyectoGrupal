<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/ticket.php";

/**
 * ============================================================
 * SECTION: DAO de tickets
 * ============================================================
 *
 * Centraliza las consultas SQL de la tabla principal `tickets`.
 */
class TicketDAO
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    /**
     * Lista todos los tickets.
     */
    public function listaTickets()
    {
        $sql = "SELECT * FROM tickets ORDER BY id";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un ticket por id.
     */
    public function getTicket($id)
    {
        $sql = "SELECT * FROM tickets WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un ticket y devuelve el id generado.
     */
    public function createTicket(Ticket $ticket)
    {
        try {
            $sql = "INSERT INTO tickets (titulo, descripcion, categoria_id, prioridad_id, estado_id, solicitante_id, tecnico_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $preparado = $this->conexion->prepare($sql);
            $preparado->execute([
                $ticket->getTitulo(),
                $ticket->getDescripcion(),
                $ticket->getCategoriaId(),
                $ticket->getPrioridadId(),
                $ticket->getEstadoId(),
                $ticket->getSolicitanteId(),
                $ticket->getTecnicoId()
            ]);

            return (int) $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza un ticket existente.
     */
    public function updateTicket(Ticket $ticket)
    {
        try {
            $sql = "UPDATE tickets
                    SET titulo = ?, descripcion = ?, categoria_id = ?, prioridad_id = ?, estado_id = ?, solicitante_id = ?, tecnico_id = ?
                    WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([
                $ticket->getTitulo(),
                $ticket->getDescripcion(),
                $ticket->getCategoriaId(),
                $ticket->getPrioridadId(),
                $ticket->getEstadoId(),
                $ticket->getSolicitanteId(),
                $ticket->getTecnicoId(),
                $ticket->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina un ticket por id.
     */
    public function deleteTicket($id)
    {
        try {
            $sql = "DELETE FROM tickets WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
