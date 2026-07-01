<?php
require_once __DIR__ . '/../controllers/usuarioController.php';

/**
 * ============================================================
 * SECTION: Rutas de usuarios
 * ============================================================
 */

$controlador = new UsuarioController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        $id != null ? $controlador->getUsuario($id) : $controlador->listaUsuarios();
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
        responderError(405, "Metodo no permitido");
}
