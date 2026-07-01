<?php

/**
 * ============================================================
 * SECTION: Modelo Historial
 * ============================================================
 *
 * Representa la tabla `historial`.
 * Guarda acciones importantes realizadas sobre un ticket.
 */
class Historial
{
    private $id;
    private $ticket_id;
    private $usuario_id;
    private $accion;
    private $valor_anterior;
    private $valor_nuevo;

    /**
     * Crea un registro de historial con valores opcionales.
     */
    public function __construct(
        $id = null,
        $ticket_id = null,
        $usuario_id = null,
        $accion = null,
        $valor_anterior = null,
        $valor_nuevo = null
    ) {
        $this->id = $id;
        $this->ticket_id = $ticket_id;
        $this->usuario_id = $usuario_id;
        $this->accion = $accion;
        $this->valor_anterior = $valor_anterior;
        $this->valor_nuevo = $valor_nuevo;
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

    public function getAccion()
    {
        return $this->accion;
    }

    public function setAccion($accion)
    {
        $this->accion = $accion;
    }

    public function getValorAnterior()
    {
        return $this->valor_anterior;
    }

    public function setValorAnterior($valor_anterior)
    {
        $this->valor_anterior = $valor_anterior;
    }

    public function getValorNuevo()
    {
        return $this->valor_nuevo;
    }

    public function setValorNuevo($valor_nuevo)
    {
        $this->valor_nuevo = $valor_nuevo;
    }
}
