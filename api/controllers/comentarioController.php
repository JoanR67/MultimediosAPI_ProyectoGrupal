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
        // DAO encargado de persistir comentarios.
        $this->dao = new ComentarioDAO();
    }

    public function listaComentarios($ticket_id = null)
    {
        // Permite filtrar por ticket solo si el id es valido.
        if ($ticket_id !== null && !esIdValido($ticket_id)) {
            responderError(400, "El filtro ticket_id debe ser un entero positivo");
            return;
        }

        // Lista comentarios globales o filtrados por ticket.
        responderJSON($this->dao->listaComentarios($ticket_id));
    }

    public function getComentario($id)
    {
        // Valida id del comentario.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Busca el comentario antes de responder.
        $comentario = $this->dao->getComentario($id);

        if (!$comentario) {
            responderError(404, "Comentario no encontrado");
            return;
        }

        responderJSON($comentario);
    }

    public function createComentario()
    {
        // Lee ticket_id, usuario_id y contenido desde JSON.
        $json = leerJsonBody();
        // Valida campos obligatorios.
        $errores = $this->validarComentario($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Inserta el comentario y recupera el id generado.
        $id = $this->dao->createComentario($this->crearModelo($json));

        if (!$id) {
            responderError(400, "No se pudo crear el comentario. Revise los ids enviados.");
            return;
        }

        responderExito("Comentario creado correctamente", ["id" => $id], 201);
    }

    public function updateComentario($id)
    {
        // PUT requiere id de comentario.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Evita actualizar comentarios inexistentes.
        if (!$this->dao->getComentario($id)) {
            responderError(404, "Comentario no encontrado");
            return;
        }

        // Lee y valida el nuevo contenido.
        $json = leerJsonBody();
        $errores = $this->validarComentario($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Prepara el modelo con el id existente.
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
        // DELETE requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Verifica existencia para devolver 404 claro.
        if (!$this->dao->getComentario($id)) {
            responderError(404, "Comentario no encontrado");
            return;
        }

        // Elimina el comentario de la tabla comentarios.
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
        // Acumula errores para devolverlos en una sola respuesta.
        $errores = [];

        if (!is_array($json)) {
            // JSON invalido o body vacio.
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
        // Convierte datos del request en objeto Comentario.
        $comentario = new Comentario();
        $comentario->setTicketId((int) $json["ticket_id"]);
        $comentario->setUsuarioId((int) $json["usuario_id"]);
        $comentario->setContenido(trim($json["contenido"]));

        return $comentario;
    }
}
