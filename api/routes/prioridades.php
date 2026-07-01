<?php
require_once __DIR__ . '/../controllers/prioridadController.php';

/**
 * ============================================================
 * SECTION: Rutas de prioridades
 * ============================================================
 */

// Instancia el controlador del catalogo de prioridades.
$controlador = new PrioridadController();
// Metodo HTTP recibido.
$metodo = $_SERVER['REQUEST_METHOD'];
// Id opcional para operaciones sobre una prioridad.
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        // GET sin id lista; con id consulta una prioridad.
        $id != null ? $controlador->getPrioridad($id) : $controlador->listaPrioridades();
        break;

    case 'POST':
        // POST crea una prioridad.
        $controlador->createPrioridad();
        break;

    case 'PUT':
        // PUT actualiza una prioridad.
        $controlador->updatePrioridad($id);
        break;

    case 'DELETE':
        // DELETE elimina una prioridad si no esta en uso.
        $controlador->deletePrioridad($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
