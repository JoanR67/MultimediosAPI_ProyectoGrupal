<?php


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
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function getCategoriaId()
    {
        return $this->categoria_id;
    }

    public function setCategoriaId($categoria_id)
    {
        $this->categoria_id = $categoria_id;
    }

    public function getPrioridadId()
    {
        return $this->prioridad_id;
    }

    public function setPrioridadId($prioridad_id)
    {
        $this->prioridad_id = $prioridad_id;
    }

    public function getEstadoId()
    {
        return $this->estado_id;
    }

    public function setEstadoId($estado_id)
    {
        $this->estado_id = $estado_id;
    }

    public function getSolicitanteId()
    {
        return $this->solicitante_id;
    }

    public function setSolicitanteId($solicitante_id)
    {
        $this->solicitante_id = $solicitante_id;
    }

    public function getTecnicoId()
    {
        return $this->tecnico_id;
    }

    public function setTecnicoId($tecnico_id)
    {
        $this->tecnico_id = $tecnico_id;
    }
}
