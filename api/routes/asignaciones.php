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

// Instancia el controlador responsable de asignaciones.
$controlador = new AsignacionController();
// Metodo HTTP recibido.
$metodo = $_SERVER['REQUEST_METHOD'];
// Id de asignacion para consultar, actualizar o eliminar.
$id = isset($_GET['id']) ? $_GET['id'] : null;
// Filtro opcional para listar asignaciones de un ticket.
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : null;

switch ($metodo) {
    case 'GET':
        // GET con id consulta una asignacion; sin id lista y puede filtrar por ticket_id.
        if ($id != null) {
            $controlador->getAsignacion($id);
        } else {
            $controlador->listaAsignaciones($ticket_id);
        }
        break;

    case 'POST':
        // POST registra una nueva asignacion.
        $controlador->createAsignacion();
        break;

    case 'PUT':
        // PUT modifica la asignacion indicada.
        $controlador->updateAsignacion($id);
        break;

    case 'DELETE':
        // DELETE elimina la asignacion indicada.
        $controlador->deleteAsignacion($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
