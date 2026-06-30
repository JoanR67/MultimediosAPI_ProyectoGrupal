<?php
require_once __DIR__ . '/../controllers/rolController.php';

$controlador = new RolController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getRol($id);
        } else {
            $controlador->listaRoles();
        }
        break;

    case 'POST':
        $controlador->createRol();
        break;

    case 'PUT':
        $controlador->updateRol($id);
        break;

    case 'DELETE':
        $controlador->deleteRol($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Opcion no permitida"]);
}