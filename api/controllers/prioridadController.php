<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/prioridadDao.php";

/**
 * ============================================================
 * SECTION: Controlador de prioridades
 * ============================================================
 *
 * Maneja el CRUD del catalogo de prioridades.
 */
class PrioridadController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new PrioridadDAO();
    }

    public function listaPrioridades()
    {
        responderJSON($this->dao->listaPrioridades());
    }

    public function getPrioridad($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $prioridad = $this->dao->getPrioridad($id);

        if (!$prioridad) {
            responderError(404, "Prioridad no encontrada");
            return;
        }

        responderJSON($prioridad);
    }

    public function createPrioridad()
    {
        $json = leerJsonBody();
        $errores = $this->validarPrioridad($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $prioridad = $this->crearModelo($json);

        if (!$this->dao->createPrioridad($prioridad)) {
            responderError(409, "No se pudo crear la prioridad. Puede que ya exista.");
            return;
        }

        responderExito("Prioridad creada correctamente", [], 201);
    }

    public function updatePrioridad($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getPrioridad($id)) {
            responderError(404, "Prioridad no encontrada");
            return;
        }

        $json = leerJsonBody();
        $errores = $this->validarPrioridad($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $prioridad = $this->crearModelo($json);
        $prioridad->setId((int) $id);

        if (!$this->dao->updatePrioridad($prioridad)) {
            responderError(409, "No se pudo actualizar la prioridad. Puede que el nombre ya exista.");
            return;
        }

        responderExito("Prioridad actualizada correctamente");
    }

    public function deletePrioridad($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getPrioridad($id)) {
            responderError(404, "Prioridad no encontrada");
            return;
        }

        if (!$this->dao->deletePrioridad($id)) {
            responderError(409, "No se puede eliminar porque la prioridad esta en uso");
            return;
        }

        responderExito("Prioridad eliminada correctamente");
    }

    /**
     * ============================================================
     * SECTION: Validaciones
     * ============================================================
     */
    private function validarPrioridad($json)
    {
        $errores = [];

        if (!is_array($json)) {
            return ["El body debe ser JSON valido"];
        }

        if (!campoTextoValido($json, "nombre")) {
            $errores[] = "El campo nombre es obligatorio";
        }

        if (!campoNumericoValido($json, "nivel")) {
            $errores[] = "El campo nivel es obligatorio y debe ser numerico";
        }

        return $errores;
    }

    private function crearModelo($json)
    {
        $prioridad = new Prioridad();
        $prioridad->setNombre(trim($json["nombre"]));
        $prioridad->setNivel((int) $json["nivel"]);

        return $prioridad;
    }
}
