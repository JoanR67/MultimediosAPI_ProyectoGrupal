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

// Instancia el controlador que contiene la logica HTTP del recurso.
$controlador = new TicketController();
// Obtiene el metodo usado por Postman o el frontend.
$metodo = $_SERVER['REQUEST_METHOD'];
// El id es opcional: si viene, se trabaja sobre un registro especifico.
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        // GET sin id lista; GET con id consulta un ticket puntual.
        if ($id != null) {
            $controlador->getTicket($id);
        } else {
            $controlador->listaTickets();
        }
        break;

    case 'POST':
        // POST crea un ticket con el JSON enviado en el body.
        $controlador->createTicket();
        break;

    case 'PUT':
        // PUT actualiza el ticket indicado por query string.
        $controlador->updateTicket($id);
        break;

    case 'DELETE':
        // DELETE elimina el ticket indicado por query string.
        $controlador->deleteTicket($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
