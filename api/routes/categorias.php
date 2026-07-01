<?php
require_once __DIR__ . '/../controllers/categoriaController.php';

/**
 * ============================================================
 * SECTION: Rutas de categorias
 * ============================================================
 */

$controlador = new CategoriaController();
$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        $id != null ? $controlador->getCategoria($id) : $controlador->listaCategorias();
        break;

    case 'POST':
        $controlador->createCategoria();
        break;

    case 'PUT':
        $controlador->updateCategoria($id);
        break;

    case 'DELETE':
        $controlador->deleteCategoria($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
