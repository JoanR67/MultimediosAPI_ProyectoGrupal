<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/asignacionDao.php";

/**
 * ============================================================
 * SECTION: Controlador de asignaciones
 * ============================================================
 *
 * Maneja la asignacion de tickets a tecnicos.
 */
class AsignacionController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new AsignacionDAO();
    }

    public function listaAsignaciones($ticket_id = null)
    {
        if ($ticket_id !== null && !esIdValido($ticket_id)) {
            responderError(400, "El filtro ticket_id debe ser un entero positivo");
            return;
        }

        responderJSON($this->dao->listaAsignaciones($ticket_id));
    }

    public function getAsignacion($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $asignacion = $this->dao->getAsignacion($id);

        if (!$asignacion) {
            responderError(404, "Asignacion no encontrada");
            return;
        }

        responderJSON($asignacion);
    }

    public function createAsignacion()
    {
        $json = leerJsonBody();
        $errores = $this->validarAsignacion($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $id = $this->dao->createAsignacion($this->crearModelo($json));

        if (!$id) {
            responderError(400, "No se pudo crear la asignacion. Revise los ids enviados.");
            return;
        }

        responderExito("Asignacion creada correctamente", ["id" => $id], 201);
    }

    public function updateAsignacion($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getAsignacion($id)) {
            responderError(404, "Asignacion no encontrada");
            return;
        }

        $json = leerJsonBody();
        $errores = $this->validarAsignacion($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $asignacion = $this->crearModelo($json);
        $asignacion->setId((int) $id);

        if (!$this->dao->updateAsignacion($asignacion)) {
            responderError(400, "No se pudo actualizar la asignacion. Revise los ids enviados.");
            return;
        }

        responderExito("Asignacion actualizada correctamente");
    }

    public function deleteAsignacion($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getAsignacion($id)) {
            responderError(404, "Asignacion no encontrada");
            return;
        }

        if (!$this->dao->deleteAsignacion($id)) {
            responderError(400, "No se pudo eliminar la asignacion");
            return;
        }

        responderExito("Asignacion eliminada correctamente");
    }

    /**
     * ============================================================
     * SECTION: Validaciones
     * ============================================================
     */
    private function validarAsignacion($json)
    {
        $errores = [];

        if (!is_array($json)) {
            return ["El body debe ser JSON valido"];
        }

        foreach (["ticket_id", "tecnico_id", "asignado_por"] as $campo) {
            if (!campoNumericoValido($json, $campo)) {
                $errores[] = "El campo $campo es obligatorio y debe ser numerico";
            }
        }

        return $errores;
    }

    private function crearModelo($json)
    {
        $asignacion = new Asignacion();
        $asignacion->setTicketId((int) $json["ticket_id"]);
        $asignacion->setTecnicoId((int) $json["tecnico_id"]);
        $asignacion->setAsignadoPor((int) $json["asignado_por"]);

        return $asignacion;
    }
}
