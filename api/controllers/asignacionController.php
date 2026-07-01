<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/asignacionDao.php";

/**
 * ============================================================
 * SECTION: Controlador de asignaciones
 * ============================================================
 *
 * Recibe las peticiones HTTP relacionadas con asignacion de tickets
 * y llama al DAO para ejecutar las operaciones en MySQL.
 */
class AsignacionController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new AsignacionDAO();
    }

    /**
     * Lista asignaciones. Permite filtrar por ticket_id.
     */
    public function listaAsignaciones($ticket_id = null)
    {
        convertirJSON($this->dao->listaAsignaciones($ticket_id));
    }

    /**
     * Devuelve una asignacion por id.
     */
    public function getAsignacion($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        $asignacion = $this->dao->getAsignacion($id);

        if (!$asignacion) {
            $this->respuestaError(404, "Asignacion no encontrada");
            return;
        }

        convertirJSON($asignacion);
    }

    /**
     * Crea una asignacion y actualiza el ticket con el tecnico.
     */
    public function createAsignacion()
    {
        $json = $this->jsonBody();

        if (!$this->datosValidos($json)) {
            $this->respuestaError(400, "Debe enviar ticket_id, tecnico_id y asignado_por");
            return;
        }

        $asignacion = $this->crearModelo($json);
        $id = $this->dao->createAsignacion($asignacion);

        if (!$id) {
            $this->respuestaError(400, "No se pudo crear la asignacion. Revise los ids enviados.");
            return;
        }

        http_response_code(201);
        convertirJSON([
            "success" => true,
            "mensaje" => "Asignacion creada correctamente",
            "id" => $id
        ]);
    }

    /**
     * Actualiza una asignacion existente.
     */
    public function updateAsignacion($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getAsignacion($id)) {
            $this->respuestaError(404, "Asignacion no encontrada");
            return;
        }

        $json = $this->jsonBody();

        if (!$this->datosValidos($json)) {
            $this->respuestaError(400, "Debe enviar ticket_id, tecnico_id y asignado_por");
            return;
        }

        $asignacion = $this->crearModelo($json);
        $asignacion->setId($id);

        $resultado = $this->dao->updateAsignacion($asignacion);

        if (!$resultado) {
            $this->respuestaError(400, "No se pudo actualizar la asignacion");
            return;
        }

        convertirJSON([
            "success" => true,
            "mensaje" => "Asignacion actualizada correctamente"
        ]);
    }

    /**
     * Elimina una asignacion por id.
     */
    public function deleteAsignacion($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getAsignacion($id)) {
            $this->respuestaError(404, "Asignacion no encontrada");
            return;
        }

        $resultado = $this->dao->deleteAsignacion($id);

        if (!$resultado) {
            $this->respuestaError(400, "No se pudo eliminar la asignacion");
            return;
        }

        convertirJSON([
            "success" => true,
            "mensaje" => "Asignacion eliminada correctamente"
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
            && isset($json["tecnico_id"]) && is_numeric($json["tecnico_id"])
            && isset($json["asignado_por"]) && is_numeric($json["asignado_por"]);
    }

    private function crearModelo($json)
    {
        $asignacion = new Asignacion();
        $asignacion->setTicketId($json["ticket_id"]);
        $asignacion->setTecnicoId($json["tecnico_id"]);
        $asignacion->setAsignadoPor($json["asignado_por"]);

        return $asignacion;
    }

    private function respuestaError($codigo, $mensaje)
    {
        http_response_code($codigo);
        convertirJSON(["error" => $mensaje]);
    }
}
