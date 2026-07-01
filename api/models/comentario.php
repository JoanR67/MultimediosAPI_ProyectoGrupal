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
        // Guarda los datos iniciales del comentario.
        $this->id = $id;
        $this->ticket_id = $ticket_id;
        $this->usuario_id = $usuario_id;
        $this->contenido = $contenido;
    }

    public function getId()
    {
        // Devuelve el id del comentario.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado para update/delete.
        $this->id = $id;
    }

    public function getTicketId()
    {
        // Devuelve el ticket comentado.
        return $this->ticket_id;
    }

    public function setTicketId($ticket_id)
    {
        // Guarda el id del ticket relacionado.
        $this->ticket_id = $ticket_id;
    }

    public function getUsuarioId()
    {
        // Devuelve el autor del comentario.
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id)
    {
        // Guarda el id del usuario autor.
        $this->usuario_id = $usuario_id;
    }

    public function getContenido()
    {
        // Devuelve el texto del comentario.
        return $this->contenido;
    }

    public function setContenido($contenido)
    {
        // Guarda el contenido enviado en el request.
        $this->contenido = $contenido;
    }
}
