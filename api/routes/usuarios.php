<?php
require_once __DIR__ . '/../controllers/usuarioController.php';

/**
 * ============================================================
 * SECTION: Rutas de usuarios
 * ============================================================
 */

// Instancia el controlador responsable de usuarios.
$controlador = new UsuarioController();
// Metodo HTTP recibido.
$metodo = $_SERVER['REQUEST_METHOD'];
// Id opcional para operar sobre un usuario especifico.
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        // GET sin id lista usuarios; con id consulta uno.
        $id != null ? $controlador->getUsuario($id) : $controlador->listaUsuarios();
        break;

    case 'POST':
        // POST crea un usuario nuevo.
        $controlador->createUsuario();
        break;

    case 'PUT':
        // PUT actualiza el usuario indicado por id.
        $controlador->updateUsuario($id);
        break;

    case 'DELETE':
        // DELETE elimina el usuario indicado si no esta en uso.
        $controlador->deleteUsuario($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
