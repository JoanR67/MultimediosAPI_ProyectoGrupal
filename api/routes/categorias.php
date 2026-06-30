<?php
require_once __DIR__ . '/../controllers/categoriaController.php';

$controlador = new CategoriaController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getCategoria($id);
        } else {
            $controlador->listaCategorias();
        }
        break;

    case 'POST':
        $controlador->createCategoria();
        break;

    case 'PUT':
        $controlador->updateCategoria($id);
        break;

    case 'DELETE':
        $controlador->deleteCategoria($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Opcion no permitida"]);
}