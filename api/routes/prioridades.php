<?php
require_once __DIR__ . '/../controllers/prioridadController.php';

$controlador = new PrioridadController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getPrioridad($id);
        } else {
            $controlador->listaPrioridades();
        }
        break;

    case 'POST':
        $controlador->createPrioridad();
        break;

    case 'PUT':
        $controlador->updatePrioridad($id);
        break;

    case 'DELETE':
        $controlador->deletePrioridad($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Opcion no permitida"]);
}