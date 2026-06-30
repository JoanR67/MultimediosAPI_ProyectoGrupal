<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/rolDao.php";

class RolController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new RolDAO();
    }

    public function listaRoles()
    {
        convertirJSON($this->dao->listaRoles());
    }

    public function getRol($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $rol = $this->dao->getRol($id);

        if (!$rol) {
            http_response_code(404);
            convertirJSON(["error" => "Rol no encontrado"]);
            return;
        }

        convertirJSON($rol);
    }

    public function createRol()
    {
        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        $rol = new Rol();
        $rol->setNombre(trim($json["nombre"]));

        $resultado = $this->dao->createRol($rol);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "Ya existe un rol con ese nombre"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function updateRol($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getRol($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Rol no encontrado"]);
            return;
        }

        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        $rol = new Rol();
        $rol->setId($id);
        $rol->setNombre(trim($json["nombre"]));

        $resultado = $this->dao->updateRol($rol);

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function deleteRol($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getRol($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Rol no encontrado"]);
            return;
        }

        $resultado = $this->dao->deleteRol($id);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "No se puede eliminar, el rol está en uso por algún usuario"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }
}