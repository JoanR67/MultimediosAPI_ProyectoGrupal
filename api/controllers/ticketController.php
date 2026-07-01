<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/ticketDao.php";

/**
 * ============================================================
 * SECTION: Controlador de tickets
 * ============================================================
 *
 * Maneja las peticiones HTTP del CRUD principal de tickets.
 */
class TicketController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new TicketDAO();
    }

    /**
     * Lista todos los tickets registrados.
     */
    public function listaTickets()
    {
        convertirJSON($this->dao->listaTickets());
    }

    /**
     * Devuelve un ticket por id.
     */
    public function getTicket($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        $ticket = $this->dao->getTicket($id);

        if (!$ticket) {
            $this->respuestaError(404, "Ticket no encontrado");
            return;
        }

        convertirJSON($ticket);
    }

    /**
     * Crea un ticket nuevo.
     */
    public function createTicket()
    {
        $json = $this->jsonBody();

        if (!$this->datosValidos($json)) {
            $this->respuestaError(400, "Debe enviar todos los datos obligatorios del ticket");
            return;
        }

        $id = $this->dao->createTicket($this->crearModelo($json));

        if (!$id) {
            $this->respuestaError(400, "No se pudo crear el ticket. Revise los ids enviados.");
            return;
        }

        http_response_code(201);
        convertirJSON([
            "success" => true,
            "mensaje" => "Ticket creado correctamente",
            "id" => $id
        ]);
    }

    /**
     * Actualiza un ticket existente.
     */
    public function updateTicket($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getTicket($id)) {
            $this->respuestaError(404, "Ticket no encontrado");
            return;
        }

        $json = $this->jsonBody();

        if (!$this->datosValidos($json)) {
            $this->respuestaError(400, "Debe enviar todos los datos obligatorios del ticket");
            return;
        }

        $ticket = $this->crearModelo($json);
        $ticket->setId($id);
        $resultado = $this->dao->updateTicket($ticket);

        if (!$resultado) {
            $this->respuestaError(400, "No se pudo actualizar el ticket");
            return;
        }

        convertirJSON([
            "success" => true,
            "mensaje" => "Ticket actualizado correctamente"
        ]);
    }

    /**
     * Elimina un ticket por id.
     */
    public function deleteTicket($id)
    {
        if (!$this->idValido($id)) {
            $this->respuestaError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getTicket($id)) {
            $this->respuestaError(404, "Ticket no encontrado");
            return;
        }

        $resultado = $this->dao->deleteTicket($id);

        if (!$resultado) {
            $this->respuestaError(409, "No se puede eliminar porque el ticket esta relacionado con otros datos");
            return;
        }

        convertirJSON([
            "success" => true,
            "mensaje" => "Ticket eliminado correctamente"
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
            && isset($json["titulo"]) && trim($json["titulo"]) !== ""
            && isset($json["descripcion"]) && trim($json["descripcion"]) !== ""
            && isset($json["categoria_id"]) && is_numeric($json["categoria_id"])
            && isset($json["prioridad_id"]) && is_numeric($json["prioridad_id"])
            && isset($json["estado_id"]) && is_numeric($json["estado_id"])
            && isset($json["solicitante_id"]) && is_numeric($json["solicitante_id"]);
    }

    private function crearModelo($json)
    {
        $ticket = new Ticket();
        $ticket->setTitulo(trim($json["titulo"]));
        $ticket->setDescripcion(trim($json["descripcion"]));
        $ticket->setCategoriaId($json["categoria_id"]);
        $ticket->setPrioridadId($json["prioridad_id"]);
        $ticket->setEstadoId($json["estado_id"]);
        $ticket->setSolicitanteId($json["solicitante_id"]);
        $ticket->setTecnicoId(isset($json["tecnico_id"]) && $json["tecnico_id"] !== "" ? $json["tecnico_id"] : null);

        return $ticket;
    }

    private function respuestaError($codigo, $mensaje)
    {
        http_response_code($codigo);
        convertirJSON(["error" => $mensaje]);
    }
}
