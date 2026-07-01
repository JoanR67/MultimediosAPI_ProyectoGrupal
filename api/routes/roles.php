<?php
require_once __DIR__ . '/../controllers/rolController.php';

/**
 * ============================================================
 * SECTION: Rutas de roles
 * ============================================================
 */

// Instancia el controlador del catalogo de roles.
$controlador = new RolController();
// Metodo HTTP recibido.
$metodo = $_SERVER['REQUEST_METHOD'];
// Id opcional para operaciones sobre un rol.
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        // GET sin id lista; con id consulta un rol.
        $id != null ? $controlador->getRol($id) : $controlador->listaRoles();
        break;

    case 'POST':
        // POST crea un rol.
        $controlador->createRol();
        break;

    case 'PUT':
        // PUT actualiza un rol.
        $controlador->updateRol($id);
        break;

    case 'DELETE':
        // DELETE elimina un rol si no esta en uso.
        $controlador->deleteRol($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
