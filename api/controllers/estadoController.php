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
        // DAO responsable de la tabla estados.
        $this->dao = new EstadoDAO();
    }

    public function listaEstados()
    {
        // Devuelve estados disponibles del ticket.
        responderJSON($this->dao->listaEstados());
    }

    public function getEstado($id)
    {
        // Valida id antes de consultar.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Busca el estado solicitado.
        $estado = $this->dao->getEstado($id);

        if (!$estado) {
            responderError(404, "Estado no encontrado");
            return;
        }

        responderJSON($estado);
    }

    public function createEstado()
    {
        // Lee el nombre desde JSON.
        $json = leerJsonBody();

        // El nombre del estado es obligatorio.
        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        // Prepara modelo Estado para insertar.
        $estado = new Estado();
        $estado->setNombre(trim($json["nombre"]));

        // Puede fallar si ya existe un estado con ese nombre.
        if (!$this->dao->createEstado($estado)) {
            responderError(409, "No se pudo crear el estado. Puede que ya exista.");
            return;
        }

        responderExito("Estado creado correctamente", [], 201);
    }

    public function updateEstado($id)
    {
        // PUT requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Verifica existencia antes de actualizar.
        if (!$this->dao->getEstado($id)) {
            responderError(404, "Estado no encontrado");
            return;
        }

        // Lee el nuevo nombre.
        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        // Modelo con id actual y datos nuevos.
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
        // DELETE requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // No se elimina un estado inexistente.
        if (!$this->dao->getEstado($id)) {
            responderError(404, "Estado no encontrado");
            return;
        }

        // Puede fallar si el estado esta usado por tickets.
        if (!$this->dao->deleteEstado($id)) {
            responderError(409, "No se puede eliminar porque el estado esta en uso");
            return;
        }

        responderExito("Estado eliminado correctamente");
    }
}
