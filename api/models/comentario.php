<?php

/**
 * ============================================================
 * SECTION: Modelo Comentario
 * ============================================================
 *
 * Representa la tabla `comentarios`.
 * Permite registrar mensajes de seguimiento asociados a un ticket.
 */
class Comentario
{
    private $id;
    private $ticket_id;
    private $usuario_id;
    private $contenido;

    /**
     * Crea un comentario con valores opcionales.
     */
    public function __construct($id = null, $ticket_id = null, $usuario_id = null, $contenido = null)
    {
        $this->id = $id;
        $this->ticket_id = $ticket_id;
        $this->usuario_id = $usuario_id;
        $this->contenido = $contenido;
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

    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;
    }

    public function getContenido()
    {
        return $this->contenido;
    }

    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }
}
