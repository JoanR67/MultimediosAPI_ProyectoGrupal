<?php
require_once "views/respuesta.php";
require_once "dao/ticketDao.php";

class TicketController
{

    private $dao;

    public function __construct()
    {
        $this->dao = new TicketDAO();
    }

    public function listaTickets()
    {
        try {
            $tickets = $this->dao->listaTickets();

            if (empty($tickets)) {
                convertirJSON([
                    "code" => "200",
                    "mensaje" => "No hay tickets para mostrar"
                ]);
                return;
            }

            convertirJSON($tickets);
        } catch (PDOException $e) {
            http_response_code(500);
            convertirJSON([
                "code" => "500",
                "error" => "Error al obtener los tickets: " . $e->getMessage()
            ]);
        }
    }

    public function getTicket($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "El id debe ser un numero mayor a 0"
            ]);
            return;
        }

        try {
            $ticket = $this->dao->getTicket($id);

            if (!$ticket) {
                http_response_code(404);
                convertirJSON([
                    "code" => "404",
                    "error" => "Ticket no encontrado"
                ]);
                return;
            }

            convertirJSON($ticket);
        } catch (PDOException $e) {
            http_response_code(500);
            convertirJSON([
                "code" => "500",
                "error" => "Error al obtener el ticket: " . $e->getMessage()
            ]);
        }
    }

    public function createTicket()
    {
        $json = json_decode(file_get_contents("php://input"), true);

        $ticket = new Ticket();

        $ticket->setTitulo($json["titulo"]);
        $ticket->setDescripcion($json["descripcion"]);
        $ticket->setCategoriaId($json["categoria_id"]);
        $ticket->setPrioridadId($json["prioridad_id"]);
        $ticket->setEstadoId($json["estado_id"]);
        $ticket->setSolicitanteId($json["solicitante_id"]);
        $ticket->setTecnicoId($json["tecnico_id"]);

        $resultado = $this->dao->createTicket($ticket);
        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function updateTicket($id)
    {
        $json = json_decode(file_get_contents("php://input"), true);

        $ticket = new Ticket();

        $ticket->setId($id);
        $ticket->setTitulo($json["titulo"]);
        $ticket->setDescripcion($json["descripcion"]);
        $ticket->setCategoriaId($json["categoria_id"]);
        $ticket->setPrioridadId($json["prioridad_id"]);
        $ticket->setEstadoId($json["estado_id"]);
        $ticket->setSolicitanteId($json["solicitante_id"]);
        $ticket->setTecnicoId($json["tecnico_id"]);

        $resultado = $this->dao->updateTicket($ticket);
        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function deleteTicket($id)
    {
        $resultado = $this->dao->deleteTicket($id);
        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }
}
