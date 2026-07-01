<?php
require_once __DIR__ . '/../controllers/categoriaController.php';

/**
 * ============================================================
 * SECTION: Rutas de categorias
 * ============================================================
 */

// Instancia el controlador del catalogo de categorias.
$controlador = new CategoriaController();
// Metodo HTTP recibido.
$metodo = $_SERVER['REQUEST_METHOD'];
// Id opcional para operaciones sobre una categoria.
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        // GET sin id lista; con id consulta una categoria.
        $id != null ? $controlador->getCategoria($id) : $controlador->listaCategorias();
        break;

    case 'POST':
        // POST crea una categoria.
        $controlador->createCategoria();
        break;

    case 'PUT':
        // PUT actualiza una categoria.
        $controlador->updateCategoria($id);
        break;

    case 'DELETE':
        // DELETE elimina una categoria si no esta en uso.
        $controlador->deleteCategoria($id);
        break;

    default:
        responderError(405, "Metodo no permitido");
}
