<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/rolDao.php";

/**
 * ============================================================
 * SECTION: Controlador de roles
 * ============================================================
 *
 * Maneja el CRUD del catalogo de roles.
 */
class RolController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new RolDAO();
    }

    public function listaRoles()
    {
        responderJSON($this->dao->listaRoles());
    }

    public function getRol($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $rol = $this->dao->getRol($id);

        if (!$rol) {
            responderError(404, "Rol no encontrado");
            return;
        }

        responderJSON($rol);
    }

    public function createRol()
    {
        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        $rol = new Rol();
        $rol->setNombre(trim($json["nombre"]));

        if (!$this->dao->createRol($rol)) {
            responderError(409, "No se pudo crear el rol. Puede que ya exista.");
            return;
        }

        responderExito("Rol creado correctamente", [], 201);
    }

    public function updateRol($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getRol($id)) {
            responderError(404, "Rol no encontrado");
            return;
        }

        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        $rol = new Rol();
        $rol->setId((int) $id);
        $rol->setNombre(trim($json["nombre"]));

        if (!$this->dao->updateRol($rol)) {
            responderError(409, "No se pudo actualizar el rol. Puede que el nombre ya exista.");
            return;
        }

        responderExito("Rol actualizado correctamente");
    }

    public function deleteRol($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getRol($id)) {
            responderError(404, "Rol no encontrado");
            return;
        }

        if (!$this->dao->deleteRol($id)) {
            responderError(409, "No se puede eliminar porque el rol esta en uso");
            return;
        }

        responderExito("Rol eliminado correctamente");
    }
}
