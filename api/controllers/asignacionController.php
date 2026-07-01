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
        // DAO encargado de leer y escribir asignaciones.
        $this->dao = new AsignacionDAO();
    }

    public function listaAsignaciones($ticket_id = null)
    {
        // ticket_id es opcional, pero si viene debe ser valido.
        if ($ticket_id !== null && !esIdValido($ticket_id)) {
            responderError(400, "El filtro ticket_id debe ser un entero positivo");
            return;
        }

        // Lista todas las asignaciones o solo las de un ticket.
        responderJSON($this->dao->listaAsignaciones($ticket_id));
    }

    public function getAsignacion($id)
    {
        // Valida id antes de buscar en BD.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Busca la asignacion solicitada.
        $asignacion = $this->dao->getAsignacion($id);

        if (!$asignacion) {
            responderError(404, "Asignacion no encontrada");
            return;
        }

        responderJSON($asignacion);
    }

    public function createAsignacion()
    {
        // Lee los ids enviados en JSON.
        $json = leerJsonBody();
        // Valida ticket, tecnico y usuario asignador.
        $errores = $this->validarAsignacion($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Crea asignacion y actualiza el tecnico del ticket desde el DAO.
        $id = $this->dao->createAsignacion($this->crearModelo($json));

        if (!$id) {
            responderError(400, "No se pudo crear la asignacion. Revise los ids enviados.");
            return;
        }

        responderExito("Asignacion creada correctamente", ["id" => $id], 201);
    }

    public function updateAsignacion($id)
    {
        // PUT necesita identificar la asignacion.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Verifica existencia para responder 404 si aplica.
        if (!$this->dao->getAsignacion($id)) {
            responderError(404, "Asignacion no encontrada");
            return;
        }

        // Lee y valida los nuevos datos.
        $json = leerJsonBody();
        $errores = $this->validarAsignacion($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Construye el modelo con el id de la URL.
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
        // DELETE necesita id valido.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // No se elimina una asignacion inexistente.
        if (!$this->dao->getAsignacion($id)) {
            responderError(404, "Asignacion no encontrada");
            return;
        }

        // El DAO tambien registra historial de eliminacion.
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
        // Junta errores de todos los campos requeridos.
        $errores = [];

        if (!is_array($json)) {
            // Body mal formado.
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
        // Convierte el JSON en objeto Asignacion para el DAO.
        $asignacion = new Asignacion();
        $asignacion->setTicketId((int) $json["ticket_id"]);
        $asignacion->setTecnicoId((int) $json["tecnico_id"]);
        $asignacion->setAsignadoPor((int) $json["asignado_por"]);

        return $asignacion;
    }
}
