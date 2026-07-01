<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/historialDao.php";

/**
 * ============================================================
 * SECTION: Controlador de historial
 * ============================================================
 *
 * Maneja registros historicos de acciones realizadas sobre tickets.
 */
class HistorialController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new HistorialDAO();
    }

    public function listaHistorial($ticket_id = null)
    {
        if ($ticket_id !== null && !esIdValido($ticket_id)) {
            responderError(400, "El filtro ticket_id debe ser un entero positivo");
            return;
        }

        responderJSON($this->dao->listaHistorial($ticket_id));
    }

    public function getHistorial($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $historial = $this->dao->getHistorial($id);

        if (!$historial) {
            responderError(404, "Registro de historial no encontrado");
            return;
        }

        responderJSON($historial);
    }

    public function createHistorial()
    {
        $json = leerJsonBody();
        $errores = $this->validarHistorial($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $id = $this->dao->createHistorial($this->crearModelo($json));

        if (!$id) {
            responderError(400, "No se pudo crear el historial. Revise los ids enviados.");
            return;
        }

        responderExito("Historial creado correctamente", ["id" => $id], 201);
    }

    public function updateHistorial($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getHistorial($id)) {
            responderError(404, "Registro de historial no encontrado");
            return;
        }

        $json = leerJsonBody();
        $errores = $this->validarHistorial($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $historial = $this->crearModelo($json);
        $historial->setId((int) $id);

        if (!$this->dao->updateHistorial($historial)) {
            responderError(400, "No se pudo actualizar el historial. Revise los ids enviados.");
            return;
        }

        responderExito("Historial actualizado correctamente");
    }

    public function deleteHistorial($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getHistorial($id)) {
            responderError(404, "Registro de historial no encontrado");
            return;
        }

        if (!$this->dao->deleteHistorial($id)) {
            responderError(400, "No se pudo eliminar el historial");
            return;
        }

        responderExito("Historial eliminado correctamente");
    }

    /**
     * ============================================================
     * SECTION: Validaciones
     * ============================================================
     */
    private function validarHistorial($json)
    {
        $errores = [];

        if (!is_array($json)) {
            return ["El body debe ser JSON valido"];
        }

        foreach (["ticket_id", "usuario_id"] as $campo) {
            if (!campoNumericoValido($json, $campo)) {
                $errores[] = "El campo $campo es obligatorio y debe ser numerico";
            }
        }

        if (!campoTextoValido($json, "accion")) {
            $errores[] = "El campo accion es obligatorio";
        }

        return $errores;
    }

    private function crearModelo($json)
    {
        $historial = new Historial();
        $historial->setTicketId((int) $json["ticket_id"]);
        $historial->setUsuarioId((int) $json["usuario_id"]);
        $historial->setAccion(trim($json["accion"]));
        $historial->setValorAnterior(isset($json["valor_anterior"]) ? $json["valor_anterior"] : null);
        $historial->setValorNuevo(isset($json["valor_nuevo"]) ? $json["valor_nuevo"] : null);

        return $historial;
    }
}
