<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/historialDao.php";

/**
 * ============================================================
 * SECTION: Controlador de historial
 * ============================================================
 *
 * Maneja los registros historicos de cambios hechos a tickets.
 */
class HistorialController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new HistorialDAO();
    }

    /**
     * Lista historial. Permite filtrar por ticket_id.
     */
    public function listaHistorial($ticket_id = null)
    {
        convertirJSON($this->dao->listaHistorial($ticket_id));
    }

    /**
     * Devuelve un registro de historial por id.
     */
    public function getHistorial($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        $historial = $this->dao->getHistorial($id);

        if (!$historial) {
            $this->respuestaError(404, "Registro de historial no encontrado");
            return;
        }

        convertirJSON($historial);
    }

    /**
     * Crea un registro manual de historial.
     */
    public function createHistorial()
    {
        $json = $this->jsonBody();

        if (!$this->datosValidos($json)) {
            $this->respuestaError(400, "Debe enviar ticket_id, usuario_id y accion");
            return;
        }

        $id = $this->dao->createHistorial($this->crearModelo($json));

        if (!$id) {
            $this->respuestaError(400, "No se pudo crear el historial. Revise los ids enviados.");
            return;
        }

        http_response_code(201);
        convertirJSON([
            "success" => true,
            "mensaje" => "Historial creado correctamente",
            "id" => $id
        ]);
    }

    /**
     * Actualiza un registro de historial.
     */
    public function updateHistorial($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getHistorial($id)) {
            $this->respuestaError(404, "Registro de historial no encontrado");
            return;
        }

        $json = $this->jsonBody();

        if (!$this->datosValidos($json)) {
            $this->respuestaError(400, "Debe enviar ticket_id, usuario_id y accion");
            return;
        }

        $historial = $this->crearModelo($json);
        $historial->setId($id);
        $resultado = $this->dao->updateHistorial($historial);

        if (!$resultado) {
            $this->respuestaError(400, "No se pudo actualizar el historial");
            return;
        }

        convertirJSON([
            "success" => true,
            "mensaje" => "Historial actualizado correctamente"
        ]);
    }

    /**
     * Elimina un registro de historial por id.
     */
    public function deleteHistorial($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getHistorial($id)) {
            $this->respuestaError(404, "Registro de historial no encontrado");
            return;
        }

        $resultado = $this->dao->deleteHistorial($id);

        if (!$resultado) {
            $this->respuestaError(400, "No se pudo eliminar el historial");
            return;
        }

        convertirJSON([
            "success" => true,
            "mensaje" => "Historial eliminado correctamente"
        ]);
    }

    /**
     * ============================================================
     * SECTION: Validaciones y helpers
     * ============================================================
     */

    private function idValido($id)
    {
        return is_numeric($id) && $id > 0;
    }

    private function jsonBody()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    private function datosValidos($json)
    {
        return is_array($json)
            && isset($json["ticket_id"]) && is_numeric($json["ticket_id"])
            && isset($json["usuario_id"]) && is_numeric($json["usuario_id"])
            && isset($json["accion"]) && trim($json["accion"]) !== "";
    }

    private function crearModelo($json)
    {
        $historial = new Historial();
        $historial->setTicketId($json["ticket_id"]);
        $historial->setUsuarioId($json["usuario_id"]);
        $historial->setAccion(trim($json["accion"]));
        $historial->setValorAnterior(isset($json["valor_anterior"]) ? $json["valor_anterior"] : null);
        $historial->setValorNuevo(isset($json["valor_nuevo"]) ? $json["valor_nuevo"] : null);

        return $historial;
    }

    private function respuestaError($codigo, $mensaje)
    {
        http_response_code($codigo);
        convertirJSON(["error" => $mensaje]);
    }
}
