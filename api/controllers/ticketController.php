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
        // El DAO encapsula todas las consultas SQL de tickets.
        $this->dao = new TicketDAO();
    }

    public function listaTickets()
    {
        // Lista todos los tickets sin filtros.
        responderJSON($this->dao->listaTickets());
    }

    public function getTicket($id)
    {
        // Valida el id recibido por query string.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Consulta el ticket solicitado.
        $ticket = $this->dao->getTicket($id);

        if (!$ticket) {
            responderError(404, "Ticket no encontrado");
            return;
        }

        responderJSON($ticket);
    }

    public function createTicket()
    {
        // Obtiene el JSON enviado por el cliente.
        $json = leerJsonBody();
        // Valida campos obligatorios antes del INSERT.
        $errores = $this->validarTicket($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Crea el registro y obtiene el id autogenerado.
        $id = $this->dao->createTicket($this->crearModelo($json));

        if (!$id) {
            responderError(400, "No se pudo crear el ticket. Revise los ids enviados.");
            return;
        }

        responderExito("Ticket creado correctamente", ["id" => $id], 201);
    }

    public function updateTicket($id)
    {
        // PUT necesita un id valido para identificar el ticket.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Evita actualizar tickets inexistentes.
        if (!$this->dao->getTicket($id)) {
            responderError(404, "Ticket no encontrado");
            return;
        }

        // Valida el body completo porque este endpoint reemplaza los datos principales.
        $json = leerJsonBody();
        $errores = $this->validarTicket($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Arma el modelo y le asigna el id recibido por query string.
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
        // DELETE requiere id entero positivo.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Verifica existencia para devolver 404 claro.
        if (!$this->dao->getTicket($id)) {
            responderError(404, "Ticket no encontrado");
            return;
        }

        // Si hay relaciones con comentarios/historial/asignaciones, el DAO devuelve false.
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
        // Acumula todos los errores de validacion del ticket.
        $errores = [];

        if (!is_array($json)) {
            // Body mal formado.
            return ["El body debe ser JSON valido"];
        }

        if (!campoTextoValido($json, "titulo")) {
            $errores[] = "El campo titulo es obligatorio";
        }

        if (!campoTextoValido($json, "descripcion")) {
            $errores[] = "El campo descripcion es obligatorio";
        }

        foreach (["categoria_id", "prioridad_id", "estado_id", "solicitante_id"] as $campo) {
            // Estos campos son llaves foraneas obligatorias.
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
        // Convierte el arreglo JSON en objeto Ticket para el DAO.
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
