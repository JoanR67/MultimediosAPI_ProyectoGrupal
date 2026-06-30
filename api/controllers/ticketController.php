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
        convertirJSON($this->dao->listaTickets());
    }

    public function getTicket($id)
    {
        convertirJSON($this->dao->getTicket($id));
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
