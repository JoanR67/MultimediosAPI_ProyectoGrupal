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
        // DAO responsable de la tabla prioridades.
        $this->dao = new PrioridadDAO();
    }

    public function listaPrioridades()
    {
        // Devuelve prioridades ordenadas por nivel.
        responderJSON($this->dao->listaPrioridades());
    }

    public function getPrioridad($id)
    {
        // Valida id antes de consultar.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Busca la prioridad solicitada.
        $prioridad = $this->dao->getPrioridad($id);

        if (!$prioridad) {
            responderError(404, "Prioridad no encontrada");
            return;
        }

        responderJSON($prioridad);
    }

    public function createPrioridad()
    {
        // Lee nombre y nivel desde JSON.
        $json = leerJsonBody();
        // Valida ambos campos antes del INSERT.
        $errores = $this->validarPrioridad($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Prepara el modelo Prioridad.
        $prioridad = $this->crearModelo($json);

        // Puede fallar si el nombre ya existe.
        if (!$this->dao->createPrioridad($prioridad)) {
            responderError(409, "No se pudo crear la prioridad. Puede que ya exista.");
            return;
        }

        responderExito("Prioridad creada correctamente", [], 201);
    }

    public function updatePrioridad($id)
    {
        // PUT requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Verifica que exista antes de actualizar.
        if (!$this->dao->getPrioridad($id)) {
            responderError(404, "Prioridad no encontrada");
            return;
        }

        // Lee y valida los nuevos datos.
        $json = leerJsonBody();
        $errores = $this->validarPrioridad($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Modelo con id actual y datos nuevos.
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
        // DELETE requiere id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // No se elimina una prioridad inexistente.
        if (!$this->dao->getPrioridad($id)) {
            responderError(404, "Prioridad no encontrada");
            return;
        }

        // Puede fallar si la prioridad esta usada por tickets.
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
        // Acumula errores de nombre y nivel.
        $errores = [];

        if (!is_array($json)) {
            // Body mal formado.
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
        // Convierte JSON en objeto Prioridad.
        $prioridad = new Prioridad();
        $prioridad->setNombre(trim($json["nombre"]));
        $prioridad->setNivel((int) $json["nivel"]);

        return $prioridad;
    }
}
