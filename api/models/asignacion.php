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
        $this->id = $id;
        $this->ticket_id = $ticket_id;
        $this->tecnico_id = $tecnico_id;
        $this->asignado_por = $asignado_por;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTicketId()
    {
        return $this->ticket_id;
    }

    public function setTicketId($ticket_id)
    {
        $this->ticket_id = $ticket_id;
    }

    public function getTecnicoId()
    {
        return $this->tecnico_id;
    }

    public function setTecnicoId($tecnico_id)
    {
        $this->tecnico_id = $tecnico_id;
    }

    public function getAsignadoPor()
    {
        return $this->asignado_por;
    }

    public function setAsignadoPor($asignado_por)
    {
        $this->asignado_por = $asignado_por;
    }
}
