<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/comentarioDao.php";

/**
 * ============================================================
 * SECTION: Controlador de comentarios
 * ============================================================
 *
 * Maneja comentarios de seguimiento asociados a tickets.
 */
class ComentarioController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new ComentarioDAO();
    }

    public function listaComentarios($ticket_id = null)
    {
        if ($ticket_id !== null && !esIdValido($ticket_id)) {
            responderError(400, "El filtro ticket_id debe ser un entero positivo");
            return;
        }

        responderJSON($this->dao->listaComentarios($ticket_id));
    }

    public function getComentario($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $comentario = $this->dao->getComentario($id);

        if (!$comentario) {
            responderError(404, "Comentario no encontrado");
            return;
        }

        responderJSON($comentario);
    }

    public function createComentario()
    {
        $json = leerJsonBody();
        $errores = $this->validarComentario($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $id = $this->dao->createComentario($this->crearModelo($json));

        if (!$id) {
            responderError(400, "No se pudo crear el comentario. Revise los ids enviados.");
            return;
        }

        responderExito("Comentario creado correctamente", ["id" => $id], 201);
    }

    public function updateComentario($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getComentario($id)) {
            responderError(404, "Comentario no encontrado");
            return;
        }

        $json = leerJsonBody();
        $errores = $this->validarComentario($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $comentario = $this->crearModelo($json);
        $comentario->setId((int) $id);

        if (!$this->dao->updateComentario($comentario)) {
            responderError(400, "No se pudo actualizar el comentario. Revise los ids enviados.");
            return;
        }

        responderExito("Comentario actualizado correctamente");
    }

    public function deleteComentario($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getComentario($id)) {
            responderError(404, "Comentario no encontrado");
            return;
        }

        if (!$this->dao->deleteComentario($id)) {
            responderError(400, "No se pudo eliminar el comentario");
            return;
        }

        responderExito("Comentario eliminado correctamente");
    }

    /**
     * ============================================================
     * SECTION: Validaciones
     * ============================================================
     */
    private function validarComentario($json)
    {
        $errores = [];

        if (!is_array($json)) {
            return ["El body debe ser JSON valido"];
        }

        foreach (["ticket_id", "usuario_id"] as $campo) {
            if (!campoNumericoValido($json, $campo)) {
                $errores[] = "El campo $campo es obligatorio y debe ser numerico";
            }
        }

        if (!campoTextoValido($json, "contenido")) {
            $errores[] = "El campo contenido es obligatorio";
        }

        return $errores;
    }

    private function crearModelo($json)
    {
        $comentario = new Comentario();
        $comentario->setTicketId((int) $json["ticket_id"]);
        $comentario->setUsuarioId((int) $json["usuario_id"]);
        $comentario->setContenido(trim($json["contenido"]));

        return $comentario;
    }
}
