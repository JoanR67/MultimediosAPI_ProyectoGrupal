<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/comentarioDao.php";

/**
 * ============================================================
 * SECTION: Controlador de comentarios
 * ============================================================
 *
 * Maneja los comentarios asociados a tickets.
 */
class ComentarioController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new ComentarioDAO();
    }

    /**
     * Lista comentarios. Permite filtrar por ticket_id.
     */
    public function listaComentarios($ticket_id = null)
    {
        convertirJSON($this->dao->listaComentarios($ticket_id));
    }

    /**
     * Devuelve un comentario por id.
     */
    public function getComentario($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        $comentario = $this->dao->getComentario($id);

        if (!$comentario) {
            $this->respuestaError(404, "Comentario no encontrado");
            return;
        }

        convertirJSON($comentario);
    }

    /**
     * Crea un comentario para un ticket.
     */
    public function createComentario()
    {
        $json = $this->jsonBody();

        if (!$this->datosValidos($json)) {
            $this->respuestaError(400, "Debe enviar ticket_id, usuario_id y contenido");
            return;
        }

        $id = $this->dao->createComentario($this->crearModelo($json));

        if (!$id) {
            $this->respuestaError(400, "No se pudo crear el comentario. Revise los ids enviados.");
            return;
        }

        http_response_code(201);
        convertirJSON([
            "success" => true,
            "mensaje" => "Comentario creado correctamente",
            "id" => $id
        ]);
    }

    /**
     * Actualiza un comentario existente.
     */
    public function updateComentario($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getComentario($id)) {
            $this->respuestaError(404, "Comentario no encontrado");
            return;
        }

        $json = $this->jsonBody();

        if (!$this->datosValidos($json)) {
            $this->respuestaError(400, "Debe enviar ticket_id, usuario_id y contenido");
            return;
        }

        $comentario = $this->crearModelo($json);
        $comentario->setId($id);
        $resultado = $this->dao->updateComentario($comentario);

        if (!$resultado) {
            $this->respuestaError(400, "No se pudo actualizar el comentario");
            return;
        }

        convertirJSON([
            "success" => true,
            "mensaje" => "Comentario actualizado correctamente"
        ]);
    }

    /**
     * Elimina un comentario por id.
     */
    public function deleteComentario($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getComentario($id)) {
            $this->respuestaError(404, "Comentario no encontrado");
            return;
        }

        $resultado = $this->dao->deleteComentario($id);

        if (!$resultado) {
            $this->respuestaError(400, "No se pudo eliminar el comentario");
            return;
        }

        convertirJSON([
            "success" => true,
            "mensaje" => "Comentario eliminado correctamente"
        ]);
    }

    /**
     * ============================================================
     * SECTION: Validaciones y helpers
     * ============================================================
     */

    private function idValido($id)
    {
        return is_numeric($id) && $id > 0;
    }

    private function jsonBody()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    private function datosValidos($json)
    {
        return is_array($json)
            && isset($json["ticket_id"]) && is_numeric($json["ticket_id"])
            && isset($json["usuario_id"]) && is_numeric($json["usuario_id"])
            && isset($json["contenido"]) && trim($json["contenido"]) !== "";
    }

    private function crearModelo($json)
    {
        $comentario = new Comentario();
        $comentario->setTicketId($json["ticket_id"]);
        $comentario->setUsuarioId($json["usuario_id"]);
        $comentario->setContenido(trim($json["contenido"]));

        return $comentario;
    }

    private function respuestaError($codigo, $mensaje)
    {
        http_response_code($codigo);
        convertirJSON(["error" => $mensaje]);
    }
}
