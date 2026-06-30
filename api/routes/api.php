<?php

require_once __DIR__ . '/../controllers/ticketController.php';

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
        http_response_code(405);
        echo json_encode(
            [
                "error" => "Opcion no permitida"
            ]
        );
}
