<?php
require_once "config/Conexion.php";
require_once "models/ticket.php";

class TicketDAO
{

    private $conexion;


    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaTickets()
    {
        $sql = "Select * FROM tickets";
        $preparado = $this->conexion->prepare($sql);

        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicket($id)
    {
        $sql = "Select * FROM tickets WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);

        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createTicket(Ticket $ticket)
    {
        $query = "INSERT INTO tickets (id, titulo, descripcion, categoria_id, prioridad_id, estado_id, solicitante_id, tecnico_id) VALUES (0, ?, ?, ?, ?, ?, ?, ?)";
        $preparado = $this->conexion->prepare($query);
        return $preparado->execute(
            [
                $ticket->getTitulo(),
                $ticket->getDescripcion(),
                $ticket->getCategoriaId(),
                $ticket->getPrioridadId(),
                $ticket->getEstadoId(),
                $ticket->getSolicitanteId(),
                $ticket->getTecnicoId(),
            ]
        );
    }

    public function updateTicket(Ticket $ticket)
    {
        $query = "UPDATE tickets SET titulo = ?, descripcion = ?, categoria_id = ?, prioridad_id = ?, estado_id = ?, solicitante_id = ?, tecnico_id = ? WHERE id = ?";
        $preparado = $this->conexion->prepare($query);
        return $preparado->execute(
            [
                $ticket->getTitulo(),
                $ticket->getDescripcion(),
                $ticket->getCategoriaId(),
                $ticket->getPrioridadId(),
                $ticket->getEstadoId(),
                $ticket->getSolicitanteId(),
                $ticket->getTecnicoId(),
                $ticket->getId(),
            ]
        );
    }

    public function deleteTicket($id)
    {
        $query = "DELETE FROM tickets WHERE id = ?";
        $preparado = $this->conexion->prepare($query);
        return $preparado->execute([$id]);
    }
}
