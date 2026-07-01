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
        // Guarda los valores iniciales del registro historico.
        $this->id = $id;
        $this->ticket_id = $ticket_id;
        $this->usuario_id = $usuario_id;
        $this->accion = $accion;
        $this->valor_anterior = $valor_anterior;
        $this->valor_nuevo = $valor_nuevo;
    }

    public function getId()
    {
        // Devuelve el id del registro historico.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado para update/delete.
        $this->id = $id;
    }

    public function getTicketId()
    {
        // Devuelve el ticket relacionado.
        return $this->ticket_id;
    }

    public function setTicketId($ticket_id)
    {
        // Guarda el id del ticket relacionado.
        $this->ticket_id = $ticket_id;
    }

    public function getUsuarioId()
    {
        // Devuelve el usuario que realizo la accion.
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id)
    {
        // Guarda el id del usuario responsable.
        $this->usuario_id = $usuario_id;
    }

    public function getAccion()
    {
        // Devuelve la accion registrada.
        return $this->accion;
    }

    public function setAccion($accion)
    {
        // Guarda la descripcion de la accion realizada.
        $this->accion = $accion;
    }

    public function getValorAnterior()
    {
        // Devuelve el valor anterior del cambio.
        return $this->valor_anterior;
    }

    public function setValorAnterior($valor_anterior)
    {
        // Guarda el valor antes del cambio.
        $this->valor_anterior = $valor_anterior;
    }

    public function getValorNuevo()
    {
        // Devuelve el valor nuevo del cambio.
        return $this->valor_nuevo;
    }

    public function setValorNuevo($valor_nuevo)
    {
        // Guarda el valor despues del cambio.
        $this->valor_nuevo = $valor_nuevo;
    }
}
