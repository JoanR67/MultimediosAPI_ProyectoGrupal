<?php

/**
 * ============================================================
 * SECTION: Modelo Asignacion
 * ============================================================
 *
 * Representa la tabla `asignaciones`.
 * Guarda quien asigno un ticket, a que tecnico y para cual ticket.
 */
class Asignacion
{
    private $id;
    private $ticket_id;
    private $tecnico_id;
    private $asignado_por;

    /**
     * Crea una asignacion con valores opcionales.
     */
    public function __construct($id = null, $ticket_id = null, $tecnico_id = null, $asignado_por = null)
    {
        // Guarda los datos iniciales de la asignacion.
        $this->id = $id;
        $this->ticket_id = $ticket_id;
        $this->tecnico_id = $tecnico_id;
        $this->asignado_por = $asignado_por;
    }

    public function getId()
    {
        // Devuelve el id de la asignacion.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado en update/delete.
        $this->id = $id;
    }

    public function getTicketId()
    {
        // Devuelve el ticket asignado.
        return $this->ticket_id;
    }

    public function setTicketId($ticket_id)
    {
        // Guarda el id del ticket relacionado.
        $this->ticket_id = $ticket_id;
    }

    public function getTecnicoId()
    {
        // Devuelve el tecnico responsable.
        return $this->tecnico_id;
    }

    public function setTecnicoId($tecnico_id)
    {
        // Guarda el id del tecnico asignado.
        $this->tecnico_id = $tecnico_id;
    }

    public function getAsignadoPor()
    {
        // Devuelve el usuario que realizo la asignacion.
        return $this->asignado_por;
    }

    public function setAsignadoPor($asignado_por)
    {
        // Guarda el id del administrador/asignador.
        $this->asignado_por = $asignado_por;
    }
}
