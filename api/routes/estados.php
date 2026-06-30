<?php
require_once __DIR__ . '/../controllers/estadoController.php';

$controlador = new EstadoController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getEstado($id);
        } else {
            $controlador->listaEstados();
        }
        break;

    case 'POST':
        $controlador->createEstado();
        break;

    case 'PUT':
        $controlador->updateEstado($id);
        break;

    case 'DELETE':
        $controlador->deleteEstado($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Opcion no permitida"]);
}