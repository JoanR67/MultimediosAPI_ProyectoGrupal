<?php
require_once __DIR__ . '/../controllers/prioridadController.php';

/**
 * ============================================================
 * SECTION: Rutas de prioridades
 * ============================================================
 */

$controlador = new PrioridadController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        $id != null ? $controlador->getPrioridad($id) : $controlador->listaPrioridades();
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
        responderError(405, "Metodo no permitido");
}
