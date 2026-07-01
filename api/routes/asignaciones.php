<?php
require_once __DIR__ . '/../controllers/asignacionController.php';

/**
 * ============================================================
 * SECTION: Rutas de asignaciones
 * ============================================================
 *
 * Endpoints:
 * - GET    /?recurso=asignaciones
 * - GET    /?recurso=asignaciones&id=1
 * - POST   /?recurso=asignaciones
 * - PUT    /?recurso=asignaciones&id=1
 * - DELETE /?recurso=asignaciones&id=1
 */

$controlador = new AsignacionController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getAsignacion($id);
        } else {
            $controlador->listaAsignaciones($ticket_id);
        }
        break;

    case 'POST':
        $controlador->createAsignacion();
        break;

    case 'PUT':
        $controlador->updateAsignacion($id);
        break;

    case 'DELETE':
        $controlador->deleteAsignacion($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
