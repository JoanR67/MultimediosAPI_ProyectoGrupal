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

$controlador = new ComentarioController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getComentario($id);
        } else {
            $controlador->listaComentarios($ticket_id);
        }
        break;

    case 'POST':
        $controlador->createComentario();
        break;

    case 'PUT':
        $controlador->updateComentario($id);
        break;

    case 'DELETE':
        $controlador->deleteComentario($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Opcion no permitida"]);
}
