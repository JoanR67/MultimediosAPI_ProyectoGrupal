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
        // DAO responsable de la tabla roles.
        $this->dao = new RolDAO();
    }

    public function listaRoles()
    {
        // Devuelve todos los roles disponibles.
        responderJSON($this->dao->listaRoles());
    }

    public function getRol($id)
    {
        // Valida id antes de consultar.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Busca el rol solicitado.
        $rol = $this->dao->getRol($id);

        if (!$rol) {
            responderError(404, "Rol no encontrado");
            return;
        }

        responderJSON($rol);
    }

    public function createRol()
    {
        // Lee el nombre del rol desde JSON.
        $json = leerJsonBody();

        // El nombre es obligatorio y no puede venir vacio.
        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        // Prepara el modelo para el DAO.
        $rol = new Rol();
        $rol->setNombre(trim($json["nombre"]));

        // Si el nombre esta duplicado, MySQL devuelve error y el DAO retorna false.
        if (!$this->dao->createRol($rol)) {
            responderError(409, "No se pudo crear el rol. Puede que ya exista.");
            return;
        }

        responderExito("Rol creado correctamente", [], 201);
    }

    public function updateRol($id)
    {
        // PUT requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Verifica que el rol exista.
        if (!$this->dao->getRol($id)) {
            responderError(404, "Rol no encontrado");
            return;
        }

        // Lee el nuevo nombre.
        $json = leerJsonBody();

        if (!campoTextoValido($json, "nombre")) {
            responderError(400, "El campo nombre es obligatorio");
            return;
        }

        // Modelo con id actual y nombre nuevo.
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
        // DELETE requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // No se elimina un rol inexistente.
        if (!$this->dao->getRol($id)) {
            responderError(404, "Rol no encontrado");
            return;
        }

        // Puede fallar si el rol esta relacionado con usuarios.
        if (!$this->dao->deleteRol($id)) {
            responderError(409, "No se puede eliminar porque el rol esta en uso");
            return;
        }

        responderExito("Rol eliminado correctamente");
    }
}
