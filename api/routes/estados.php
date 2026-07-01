<?php
require_once __DIR__ . '/../controllers/estadoController.php';

/**
 * ============================================================
 * SECTION: Rutas de estados
 * ============================================================
 */

$controlador = new EstadoController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        $id != null ? $controlador->getEstado($id) : $controlador->listaEstados();
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
        responderError(405, "Metodo no permitido");
}
