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

$controlador = new HistorialController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getHistorial($id);
        } else {
            $controlador->listaHistorial($ticket_id);
        }
        break;

    case 'POST':
        $controlador->createHistorial();
        break;

    case 'PUT':
        $controlador->updateHistorial($id);
        break;

    case 'DELETE':
        $controlador->deleteHistorial($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Opcion no permitida"]);
}
