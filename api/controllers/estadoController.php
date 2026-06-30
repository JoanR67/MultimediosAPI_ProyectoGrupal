<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/estadoDao.php";

class EstadoController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new EstadoDAO();
    }

    public function listaEstados()
    {
        convertirJSON($this->dao->listaEstados());
    }

    public function getEstado($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $estado = $this->dao->getEstado($id);

        if (!$estado) {
            http_response_code(404);
            convertirJSON(["error" => "Estado no encontrado"]);
            return;
        }

        convertirJSON($estado);
    }

    public function createEstado()
    {
        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        $estado = new Estado();
        $estado->setNombre(trim($json["nombre"]));

        $resultado = $this->dao->createEstado($estado);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "Ya existe un estado con ese nombre"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function updateEstado($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getEstado($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Estado no encontrado"]);
            return;
        }

        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        $estado = new Estado();
        $estado->setId($id);
        $estado->setNombre(trim($json["nombre"]));

        $resultado = $this->dao->updateEstado($estado);

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function deleteEstado($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getEstado($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Estado no encontrado"]);
            return;
        }

        $resultado = $this->dao->deleteEstado($id);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "No se puede eliminar, el estado está en uso por algún ticket"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }
}