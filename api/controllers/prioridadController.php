<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/prioridadDao.php";

class PrioridadController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new PrioridadDAO();
    }

    public function listaPrioridades()
    {
        convertirJSON($this->dao->listaPrioridades());
    }

    public function getPrioridad($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $prioridad = $this->dao->getPrioridad($id);

        if (!$prioridad) {
            http_response_code(404);
            convertirJSON(["error" => "Prioridad no encontrada"]);
            return;
        }

        convertirJSON($prioridad);
    }

    public function createPrioridad()
    {
        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        if (!isset($json["nivel"]) || !is_numeric($json["nivel"])) {
            http_response_code(400);
            convertirJSON(["error" => "El campo nivel es obligatorio y debe ser numérico"]);
            return;
        }

        $prioridad = new Prioridad();
        $prioridad->setNombre(trim($json["nombre"]));
        $prioridad->setNivel($json["nivel"]);

        $resultado = $this->dao->createPrioridad($prioridad);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "Ya existe una prioridad con ese nombre"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function updatePrioridad($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getPrioridad($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Prioridad no encontrada"]);
            return;
        }

        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        if (!isset($json["nivel"]) || !is_numeric($json["nivel"])) {
            http_response_code(400);
            convertirJSON(["error" => "El campo nivel es obligatorio y debe ser numérico"]);
            return;
        }

        $prioridad = new Prioridad();
        $prioridad->setId($id);
        $prioridad->setNombre(trim($json["nombre"]));
        $prioridad->setNivel($json["nivel"]);

        $resultado = $this->dao->updatePrioridad($prioridad);

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function deletePrioridad($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getPrioridad($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Prioridad no encontrada"]);
            return;
        }

        $resultado = $this->dao->deletePrioridad($id);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "No se puede eliminar, la prioridad está en uso por algún ticket"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }
}