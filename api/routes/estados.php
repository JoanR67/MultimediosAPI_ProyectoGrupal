<?php
require_once __DIR__ . '/../controllers/estadoController.php';

/**
 * ============================================================
 * SECTION: Rutas de estados
 * ============================================================
 */

// Instancia el controlador del catalogo de estados.
$controlador = new EstadoController();
// Metodo HTTP recibido.
$metodo = $_SERVER['REQUEST_METHOD'];
// Id opcional para operaciones sobre un estado.
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        // GET sin id lista; con id consulta un estado.
        $id != null ? $controlador->getEstado($id) : $controlador->listaEstados();
        break;

    case 'POST':
        // POST crea un estado.
        $controlador->createEstado();
        break;

    case 'PUT':
        // PUT actualiza un estado.
        $controlador->updateEstado($id);
        break;

    case 'DELETE':
        // DELETE elimina un estado si no esta en uso.
        $controlador->deleteEstado($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
