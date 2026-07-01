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

    public function listaTickets()
    {
        responderJSON($this->dao->listaTickets());
    }

    public function getTicket($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $ticket = $this->dao->getTicket($id);

        if (!$ticket) {
            responderError(404, "Ticket no encontrado");
            return;
        }

        responderJSON($ticket);
    }

    public function createTicket()
    {
        $json = leerJsonBody();
        $errores = $this->validarTicket($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $id = $this->dao->createTicket($this->crearModelo($json));

        if (!$id) {
            responderError(400, "No se pudo crear el ticket. Revise los ids enviados.");
            return;
        }

        responderExito("Ticket creado correctamente", ["id" => $id], 201);
    }

    public function updateTicket($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getTicket($id)) {
            responderError(404, "Ticket no encontrado");
            return;
        }

        $json = leerJsonBody();
        $errores = $this->validarTicket($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $ticket = $this->crearModelo($json);
        $ticket->setId((int) $id);

        if (!$this->dao->updateTicket($ticket)) {
            responderError(400, "No se pudo actualizar el ticket. Revise los ids enviados.");
            return;
        }

        responderExito("Ticket actualizado correctamente");
    }

    public function deleteTicket($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getTicket($id)) {
            responderError(404, "Ticket no encontrado");
            return;
        }

        if (!$this->dao->deleteTicket($id)) {
            responderError(409, "No se puede eliminar porque el ticket esta relacionado con otros datos");
            return;
        }

        responderExito("Ticket eliminado correctamente");
    }

    /**
     * ============================================================
     * SECTION: Validaciones
     * ============================================================
     */
    private function validarTicket($json)
    {
        $errores = [];

        if (!is_array($json)) {
            return ["El body debe ser JSON valido"];
        }

        if (!campoTextoValido($json, "titulo")) {
            $errores[] = "El campo titulo es obligatorio";
        }

        if (!campoTextoValido($json, "descripcion")) {
            $errores[] = "El campo descripcion es obligatorio";
        }

        foreach (["categoria_id", "prioridad_id", "estado_id", "solicitante_id"] as $campo) {
            if (!campoNumericoValido($json, $campo)) {
                $errores[] = "El campo $campo es obligatorio y debe ser numerico";
            }
        }

        if (isset($json["tecnico_id"]) && $json["tecnico_id"] !== null && $json["tecnico_id"] !== "" && !is_numeric($json["tecnico_id"])) {
            $errores[] = "El campo tecnico_id debe ser numerico o null";
        }

        return $errores;
    }

    private function crearModelo($json)
    {
        $ticket = new Ticket();
        $ticket->setTitulo(trim($json["titulo"]));
        $ticket->setDescripcion(trim($json["descripcion"]));
        $ticket->setCategoriaId((int) $json["categoria_id"]);
        $ticket->setPrioridadId((int) $json["prioridad_id"]);
        $ticket->setEstadoId((int) $json["estado_id"]);
        $ticket->setSolicitanteId((int) $json["solicitante_id"]);
        $ticket->setTecnicoId(isset($json["tecnico_id"]) && $json["tecnico_id"] !== "" ? $json["tecnico_id"] : null);

        return $ticket;
    }
}
