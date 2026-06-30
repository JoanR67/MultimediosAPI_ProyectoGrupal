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
                "error" => "El id debe ser un número mayor a 0"
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
                "error" => "El body de la petición debe ser un JSON válido"
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

        // titulo y descripcion deben ser texto (un array o numero rompe trim/strlen)
        $noTexto = [];
        if (!is_string($json["titulo"])) {
            $noTexto[] = "titulo";
        }
        if (!is_string($json["descripcion"])) {
            $noTexto[] = "descripcion";
        }
        if (!empty($noTexto)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "Deben ser texto: " . implode(", ", $noTexto)
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
                "error" => "No pueden estar vacíos: " . implode(", ", $vacios)
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
                "error" => "Superan el máximo de caracteres: " . implode(", ", $excedidos)
            ]);
            return;
        }

        // 5. Los *_id deben ser numeros enteros (se reportan todos los que no lo sean)
        $ids = ["categoria_id", "prioridad_id", "estado_id", "solicitante_id"];
        $noEnteros = [];
        foreach ($ids as $campo) {
            if (is_bool($json[$campo]) || filter_var($json[$campo], FILTER_VALIDATE_INT) === false) {
                $noEnteros[] = $campo;
            }
        }
        if (!empty($noEnteros)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "Deben ser un número entero: " . implode(", ", $noEnteros)
            ]);
            return;
        }

        // 7. tecnico_id es opcional; si viene (no null) debe ser un numero entero
        $tecnico_id = null;
        if (isset($json["tecnico_id"]) && $json["tecnico_id"] !== null) {
            if (is_bool($json["tecnico_id"]) || filter_var($json["tecnico_id"], FILTER_VALIDATE_INT) === false) {
                http_response_code(400);
                convertirJSON([
                    "code" => "400",
                    "error" => "El campo tecnico_id debe ser un número entero"
                ]);
                return;
            }
            $tecnico_id = $json["tecnico_id"];
        }

        $ticket = new Ticket();
        $ticket->setTitulo(trim($json["titulo"]));
        $ticket->setDescripcion(trim($json["descripcion"]));
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
                    "error" => "Datos inválidos: verifique que la categoría, prioridad, estado, solicitante y técnico existan"
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
        // El id debe ser numerico y mayor a 0
        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "El id debe ser un número mayor a 0"
            ]);
            return;
        }

        $json = json_decode(file_get_contents("php://input"), true);

        // 1. El body debe venir y ser un JSON valido
        if (!is_array($json)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "El body de la petición debe ser un JSON válido"
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

        // titulo y descripcion deben ser texto (un array o numero rompe trim/strlen)
        $noTexto = [];
        if (!is_string($json["titulo"])) {
            $noTexto[] = "titulo";
        }
        if (!is_string($json["descripcion"])) {
            $noTexto[] = "descripcion";
        }
        if (!empty($noTexto)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "Deben ser texto: " . implode(", ", $noTexto)
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
                "error" => "No pueden estar vacíos: " . implode(", ", $vacios)
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
                "error" => "Superan el máximo de caracteres: " . implode(", ", $excedidos)
            ]);
            return;
        }

        // 5. Los *_id deben ser numeros enteros (se reportan todos los que no lo sean)
        $ids = ["categoria_id", "prioridad_id", "estado_id", "solicitante_id"];
        $noEnteros = [];
        foreach ($ids as $campo) {
            if (is_bool($json[$campo]) || filter_var($json[$campo], FILTER_VALIDATE_INT) === false) {
                $noEnteros[] = $campo;
            }
        }
        if (!empty($noEnteros)) {
            http_response_code(400);
            convertirJSON([
                "code" => "400",
                "error" => "Deben ser un número entero: " . implode(", ", $noEnteros)
            ]);
            return;
        }

        // 7. tecnico_id es opcional; si viene (no null) debe ser un numero entero
        $tecnico_id = null;
        if (isset($json["tecnico_id"]) && $json["tecnico_id"] !== null) {
            if (is_bool($json["tecnico_id"]) || filter_var($json["tecnico_id"], FILTER_VALIDATE_INT) === false) {
                http_response_code(400);
                convertirJSON([
                    "code" => "400",
                    "error" => "El campo tecnico_id debe ser un número entero"
                ]);
                return;
            }
            $tecnico_id = $json["tecnico_id"];
        }

        try {
            // El ticket debe existir antes de actualizar
            if (!$this->dao->getTicket($id)) {
                http_response_code(404);
                convertirJSON([
                    "code" => "404",
                    "error" => "Ticket no encontrado"
                ]);
                return;
            }

            $ticket = new Ticket();
            $ticket->setId($id);
            $ticket->setTitulo(trim($json["titulo"]));
            $ticket->setDescripcion(trim($json["descripcion"]));
            $ticket->setCategoriaId($json["categoria_id"]);
            $ticket->setPrioridadId($json["prioridad_id"]);
            $ticket->setEstadoId($json["estado_id"]);
            $ticket->setSolicitanteId($json["solicitante_id"]);
            $ticket->setTecnicoId($tecnico_id);

            // 6. FK existentes: si un id no existe, MySQL lanza error de integridad
            $resultado = $this->dao->updateTicket($ticket);
            convertirJSON([
                "code" => "200",
                "success" => $resultado
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(400);
                convertirJSON([
                    "code" => "400",
                    "error" => "Datos inválidos: verifique que la categoría, prioridad, estado, solicitante y técnico existan"
                ]);
            } else {
                http_response_code(500);
                convertirJSON([
                    "code" => "500",
                    "error" => "Error al actualizar el ticket: " . $e->getMessage()
                ]);
            }
        }
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
