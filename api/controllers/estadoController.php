<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/estadoDao.php";

/**
 * ============================================================
 * SECTION: Controlador de estados
 * ============================================================
 *
 * Maneja el CRUD del catalogo de estados del ticket.
 */
class EstadoController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new EstadoDAO();
    }

    public function listaEstados()
    {
        responderJSON($this->dao->listaEstados());
    }

    public function getEstado($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $estado = $this->dao->getEstado($id);

        if (!$estado) {
            responderError(404, "Estado no encontrado");
            return;
        }

        responderJSON($estado);
    }

    public function createEstado()
    {
        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        $estado = new Estado();
        $estado->setNombre(trim($json["nombre"]));

        if (!$this->dao->createEstado($estado)) {
            responderError(409, "No se pudo crear el estado. Puede que ya exista.");
            return;
        }

        responderExito("Estado creado correctamente", [], 201);
    }

    public function updateEstado($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getEstado($id)) {
            responderError(404, "Estado no encontrado");
            return;
        }

        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        $estado = new Estado();
        $estado->setId((int) $id);
        $estado->setNombre(trim($json["nombre"]));

        if (!$this->dao->updateEstado($estado)) {
            responderError(409, "No se pudo actualizar el estado. Puede que el nombre ya exista.");
            return;
        }

        responderExito("Estado actualizado correctamente");
    }

    public function deleteEstado($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getEstado($id)) {
            responderError(404, "Estado no encontrado");
            return;
        }

        if (!$this->dao->deleteEstado($id)) {
            responderError(409, "No se puede eliminar porque el estado esta en uso");
            return;
        }

        responderExito("Estado eliminado correctamente");
    }
}
