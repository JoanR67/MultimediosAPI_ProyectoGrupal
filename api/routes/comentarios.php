<?php
require_once __DIR__ . '/../controllers/comentarioController.php';

/**
 * ============================================================
 * SECTION: Rutas de comentarios
 * ============================================================
 *
 * Endpoints:
 * - GET    /?recurso=comentarios
 * - GET    /?recurso=comentarios&id=1
 * - POST   /?recurso=comentarios
 * - PUT    /?recurso=comentarios&id=1
 * - DELETE /?recurso=comentarios&id=1
 */

// Instancia el controlador responsable de comentarios.
$controlador = new ComentarioController();
// Metodo HTTP recibido.
$metodo = $_SERVER['REQUEST_METHOD'];
// Id de comentario para operaciones sobre un registro.
$id = isset($_GET['id']) ? $_GET['id'] : null;
// Filtro opcional para listar comentarios de un ticket.
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : null;

switch ($metodo) {
    case 'GET':
        // GET con id consulta un comentario; sin id lista comentarios.
        if ($id != null) {
            $controlador->getComentario($id);
        } else {
            $controlador->listaComentarios($ticket_id);
        }
        break;

    case 'POST':
        // POST crea un comentario asociado a un ticket.
        $controlador->createComentario();
        break;

    case 'PUT':
        // PUT actualiza el comentario indicado.
        $controlador->updateComentario($id);
        break;

    case 'DELETE':
        // DELETE elimina el comentario indicado.
        $controlador->deleteComentario($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
