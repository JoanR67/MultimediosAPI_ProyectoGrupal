<?php
require_once __DIR__ . '/../controllers/historialController.php';

/**
 * ============================================================
 * SECTION: Rutas de historial
 * ============================================================
 *
 * Endpoints:
 * - GET    /?recurso=historial
 * - GET    /?recurso=historial&id=1
 * - POST   /?recurso=historial
 * - PUT    /?recurso=historial&id=1
 * - DELETE /?recurso=historial&id=1
 */

// Instancia el controlador responsable del historial.
$controlador = new HistorialController();
// Metodo HTTP recibido.
$metodo = $_SERVER['REQUEST_METHOD'];
// Id del registro historico para operaciones puntuales.
$id = isset($_GET['id']) ? $_GET['id'] : null;
// Filtro opcional para consultar historial de un ticket.
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : null;

switch ($metodo) {
    case 'GET':
        // GET con id consulta un registro; sin id lista historial.
        if ($id != null) {
            $controlador->getHistorial($id);
        } else {
            $controlador->listaHistorial($ticket_id);
        }
        break;

    case 'POST':
        // POST crea un registro manual de historial.
        $controlador->createHistorial();
        break;

    case 'PUT':
        // PUT actualiza el registro historico indicado.
        $controlador->updateHistorial($id);
        break;

    case 'DELETE':
        // DELETE elimina el registro historico indicado.
        $controlador->deleteHistorial($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
