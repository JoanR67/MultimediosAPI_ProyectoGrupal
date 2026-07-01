<?php
require_once __DIR__ . '/../controllers/ticketController.php';

/**
 * ============================================================
 * SECTION: Rutas de tickets
 * ============================================================
 *
 * Endpoints:
 * - GET    /?recurso=tickets
 * - GET    /?recurso=tickets&id=1
 * - POST   /?recurso=tickets
 * - PUT    /?recurso=tickets&id=1
 * - DELETE /?recurso=tickets&id=1
 *
 * Si no se envia `recurso`, index.php usa tickets por defecto.
 */

$controlador = new TicketController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getTicket($id);
        } else {
            $controlador->listaTickets();
        }
        break;

    case 'POST':
        $controlador->createTicket();
        break;

    case 'PUT':
        $controlador->updateTicket($id);
        break;

    case 'DELETE':
        $controlador->deleteTicket($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
