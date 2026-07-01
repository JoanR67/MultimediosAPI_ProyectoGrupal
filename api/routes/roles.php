<?php
require_once __DIR__ . '/../controllers/rolController.php';

/**
 * ============================================================
 * SECTION: Rutas de roles
 * ============================================================
 */

$controlador = new RolController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        $id != null ? $controlador->getRol($id) : $controlador->listaRoles();
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
        responderError(405, "Metodo no permitido");
}
