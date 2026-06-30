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

        // 1. El body debe venir y ser un JSON valido
        if (!is_array($json)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "El body de la peticion debe ser un JSON valido"
            ]);
            return;
        }

        // 2. Campos obligatorios presentes (se reportan TODOS los que falten)
        $obligatorios = ["titulo", "descripcion", "categoria_id", "prioridad_id", "estado_id", "solicitante_id"];
        $faltantes = [];
        foreach ($obligatorios as $campo) {
            if (!isset($json[$campo])) {
                $faltantes[] = $campo;
            }
        }
        if (!empty($faltantes)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "Faltan campos obligatorios: " . implode(", ", $faltantes)
            ]);
            return;
        }

        // 3. titulo y descripcion no vacios (se reporta cual o cuales lo estan)
        $vacios = [];
        if (trim($json["titulo"]) === "") {
            $vacios[] = "titulo";
        }
        if (trim($json["descripcion"]) === "") {
            $vacios[] = "descripcion";
        }
        if (!empty($vacios)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "No pueden estar vacios: " . implode(", ", $vacios)
            ]);
            return;
        }

        // 4. Longitud maxima (se reporta cual o cuales se exceden)
        $maximos = ["titulo" => 150];
        $excedidos = [];
        foreach ($maximos as $campo => $max) {
            if (strlen($json[$campo]) > $max) {
                $excedidos[] = $campo . " (max " . $max . ")";
            }
        }
        if (!empty($excedidos)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "Superan el maximo de caracteres: " . implode(", ", $excedidos)
            ]);
            return;
        }

        // 5. Los *_id deben ser numericos (se reportan todos los que no lo sean)
        $ids = ["categoria_id", "prioridad_id", "estado_id", "solicitante_id"];
        $noNumericos = [];
        foreach ($ids as $campo) {
            if (!is_numeric($json[$campo])) {
                $noNumericos[] = $campo;
            }
        }
        if (!empty($noNumericos)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "Deben ser numericos: " . implode(", ", $noNumericos)
            ]);
            return;
        }

        // 7. tecnico_id es opcional; si viene (no null) debe ser numerico
        $tecnico_id = null;
        if (isset($json["tecnico_id"]) && $json["tecnico_id"] !== null) {
            if (!is_numeric($json["tecnico_id"])) {
                http_response_code(400);
                convertirJSON([
                    "code" => "400",
                    "error" => "El campo tecnico_id debe ser numerico"
                ]);
                return;
            }
            $tecnico_id = $json["tecnico_id"];
        }

        $ticket = new Ticket();
        $ticket->setTitulo($json["titulo"]);
        $ticket->setDescripcion($json["descripcion"]);
        $ticket->setCategoriaId($json["categoria_id"]);
        $ticket->setPrioridadId($json["prioridad_id"]);
        $ticket->setEstadoId($json["estado_id"]);
        $ticket->setSolicitanteId($json["solicitante_id"]);
        $ticket->setTecnicoId($tecnico_id);

        // 6. FK existentes: si un id no existe, MySQL lanza error de integridad
        try {
            $resultado = $this->dao->createTicket($ticket);
            convertirJSON([
                "code" => "200",
                "success" => $resultado
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(400);
                convertirJSON([
                    "code" => "400",
                    "error" => "Datos invalidos: verifique que la categoria, prioridad, estado, solicitante y tecnico existan"
                ]);
            } else {
                http_response_code(500);
                convertirJSON([
                    "code" => "500",
                    "error" => "Error al crear el ticket: " . $e->getMessage()
                ]);
            }
        }
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
