<?php
require_once __DIR__ . '/../controllers/usuarioController.php';

$controlador = new UsuarioController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id != null) {
            $controlador->getUsuario($id);
        } else {
            $controlador->listaUsuarios();
        }
        break;

    case 'POST':
        $controlador->createUsuario();
        break;

    case 'PUT':
        $controlador->updateUsuario($id);
        break;

    case 'DELETE':
        $controlador->deleteUsuario($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Opcion no permitida"]);
}