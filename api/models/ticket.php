<?php

/**
 * ============================================================
 * SECTION: Modelo Ticket
 * ============================================================
 *
 * Representa la tabla principal `tickets`.
 */
class Ticket
{
    private $id;
    private $titulo;
    private $descripcion;
    private $categoria_id;
    private $prioridad_id;
    private $estado_id;
    private $solicitante_id;
    private $tecnico_id;

    /**
     * Crea un ticket con valores opcionales.
     */
    public function __construct(
        $id = null,
        $titulo = null,
        $descripcion = null,
        $categoria_id = null,
        $prioridad_id = null,
        $estado_id = null,
        $solicitante_id = null,
        $tecnico_id = null
    ) {
        // Guarda los valores iniciales del ticket en memoria.
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->categoria_id = $categoria_id;
        $this->prioridad_id = $prioridad_id;
        $this->estado_id = $estado_id;
        $this->solicitante_id = $solicitante_id;
        $this->tecnico_id = $tecnico_id;
    }

    public function getId()
    {
        // Devuelve el identificador del ticket.
        return $this->id;
    }

    public function setId($id)
    {
        // Asigna el id usado en actualizaciones o eliminaciones.
        $this->id = $id;
    }

    public function getTitulo()
    {
        // Devuelve el titulo visible del ticket.
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        // Guarda el titulo recibido desde el request.
        $this->titulo = $titulo;
    }

    public function getDescripcion()
    {
        // Devuelve la descripcion del problema.
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        // Guarda la descripcion enviada por el usuario.
        $this->descripcion = $descripcion;
    }

    public function getCategoriaId()
    {
        // Devuelve la categoria asociada al ticket.
        return $this->categoria_id;
    }

    public function setCategoriaId($categoria_id)
    {
        // Asigna la categoria validada por el controlador.
        $this->categoria_id = $categoria_id;
    }

    public function getPrioridadId()
    {
        // Devuelve la prioridad asociada al ticket.
        return $this->prioridad_id;
    }

    public function setPrioridadId($prioridad_id)
    {
        // Asigna la prioridad del ticket.
        $this->prioridad_id = $prioridad_id;
    }

    public function getEstadoId()
    {
        // Devuelve el estado actual del ticket.
        return $this->estado_id;
    }

    public function setEstadoId($estado_id)
    {
        // Asigna el estado del ciclo de vida del ticket.
        $this->estado_id = $estado_id;
    }

    public function getSolicitanteId()
    {
        // Devuelve el usuario que creo o solicito el ticket.
        return $this->solicitante_id;
    }

    public function setSolicitanteId($solicitante_id)
    {
        // Asigna el usuario solicitante.
        $this->solicitante_id = $solicitante_id;
    }

    public function getTecnicoId()
    {
        // Devuelve el tecnico asignado, si existe.
        return $this->tecnico_id;
    }

    public function setTecnicoId($tecnico_id)
    {
        // Asigna tecnico o null cuando aun no hay asignacion.
        $this->tecnico_id = $tecnico_id;
    }
}
