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
        $this->dao = new CategoriaDAO();
    }

    public function listaCategorias()
    {
        responderJSON($this->dao->listaCategorias());
    }

    public function getCategoria($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $categoria = $this->dao->getCategoria($id);

        if (!$categoria) {
            responderError(404, "Categoria no encontrada");
            return;
        }

        responderJSON($categoria);
    }

    public function createCategoria()
    {
        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        $categoria = new Categoria();
        $categoria->setNombre(trim($json["nombre"]));

        if (!$this->dao->createCategoria($categoria)) {
            responderError(409, "No se pudo crear la categoria. Puede que ya exista.");
            return;
        }

        responderExito("Categoria creada correctamente", [], 201);
    }

    public function updateCategoria($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getCategoria($id)) {
            responderError(404, "Categoria no encontrada");
            return;
        }

        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

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
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getCategoria($id)) {
            responderError(404, "Categoria no encontrada");
            return;
        }

        if (!$this->dao->deleteCategoria($id)) {
            responderError(409, "No se puede eliminar porque la categoria esta en uso");
            return;
        }

        responderExito("Categoria eliminada correctamente");
    }
}
