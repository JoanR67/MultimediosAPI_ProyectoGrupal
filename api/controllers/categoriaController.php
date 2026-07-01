<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/categoriaDao.php";

/**
 * ============================================================
 * SECTION: Controlador de categorias
 * ============================================================
 *
 * Maneja el CRUD del catalogo de categorias de tickets.
 */
class CategoriaController
{
    private $dao;

    public function __construct()
    {
        // DAO responsable de la tabla categorias.
        $this->dao = new CategoriaDAO();
    }

    public function listaCategorias()
    {
        // Devuelve todas las categorias.
        responderJSON($this->dao->listaCategorias());
    }

    public function getCategoria($id)
    {
        // Valida id antes de consultar.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Busca la categoria solicitada.
        $categoria = $this->dao->getCategoria($id);

        if (!$categoria) {
            responderError(404, "Categoria no encontrada");
            return;
        }

        responderJSON($categoria);
    }

    public function createCategoria()
    {
        // Lee el JSON con el nombre de categoria.
        $json = leerJsonBody();

        // El nombre es obligatorio.
        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        // Prepara el modelo para insertar.
        $categoria = new Categoria();
        $categoria->setNombre(trim($json["nombre"]));

        // Puede fallar si ya existe una categoria con ese nombre.
        if (!$this->dao->createCategoria($categoria)) {
            responderError(409, "No se pudo crear la categoria. Puede que ya exista.");
            return;
        }

        responderExito("Categoria creada correctamente", [], 201);
    }

    public function updateCategoria($id)
    {
        // PUT requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Verifica existencia antes de actualizar.
        if (!$this->dao->getCategoria($id)) {
            responderError(404, "Categoria no encontrada");
            return;
        }

        // Lee el nuevo nombre.
        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        // Modelo con id actual y datos nuevos.
        $categoria = new Categoria();
        $categoria->setId((int) $id);
        $categoria->setNombre(trim($json["nombre"]));

        if (!$this->dao->updateCategoria($categoria)) {
            responderError(409, "No se pudo actualizar la categoria. Puede que el nombre ya exista.");
            return;
        }

        responderExito("Categoria actualizada correctamente");
    }

    public function deleteCategoria($id)
    {
        // DELETE requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // No se elimina una categoria inexistente.
        if (!$this->dao->getCategoria($id)) {
            responderError(404, "Categoria no encontrada");
            return;
        }

        // Puede fallar si la categoria esta usada por tickets.
        if (!$this->dao->deleteCategoria($id)) {
            responderError(409, "No se puede eliminar porque la categoria esta en uso");
            return;
        }

        responderExito("Categoria eliminada correctamente");
    }
}
