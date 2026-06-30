<?php
require_once __DIR__ . '/../controllers/autenticacionController.php';

$controlador = new AutenticacionController();
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        $controlador->login();
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Opcion no permitida"]);
}