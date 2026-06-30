<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/categoriaDao.php";

class CategoriaController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new CategoriaDAO();
    }

    public function listaCategorias()
    {
        convertirJSON($this->dao->listaCategorias());
    }

    public function getCategoria($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $categoria = $this->dao->getCategoria($id);

        if (!$categoria) {
            http_response_code(404);
            convertirJSON(["error" => "Categoria no encontrada"]);
            return;
        }

        convertirJSON($categoria);
    }

    public function createCategoria()
    {
        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        $categoria = new Categoria();
        $categoria->setNombre(trim($json["nombre"]));

        $resultado = $this->dao->createCategoria($categoria);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "Ya existe una categoria con ese nombre"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function updateCategoria($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getCategoria($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Categoria no encontrada"]);
            return;
        }

        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        $categoria = new Categoria();
        $categoria->setId($id);
        $categoria->setNombre(trim($json["nombre"]));

        $resultado = $this->dao->updateCategoria($categoria);

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function deleteCategoria($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getCategoria($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Categoria no encontrada"]);
            return;
        }

        $resultado = $this->dao->deleteCategoria($id);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "No se puede eliminar, la categoria está en uso por algún ticket"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }
}