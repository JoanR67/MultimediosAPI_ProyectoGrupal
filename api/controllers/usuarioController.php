<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/usuarioDao.php";

class UsuarioController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UsuarioDAO();
    }

    public function listaUsuarios()
    {
        convertirJSON($this->dao->listaUsuarios());
    }

    public function getUsuario($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $usuario = $this->dao->getUsuario($id);

        if (!$usuario) {
            http_response_code(404);
            convertirJSON(["error" => "Usuario no encontrado"]);
            return;
        }

        convertirJSON($usuario);
    }

    public function createUsuario()
    {
        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        if (!isset($json["email"]) || !filter_var($json["email"], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            convertirJSON(["error" => "El correo no es válido"]);
            return;
        }

        if (!isset($json["password"]) || strlen($json["password"]) < 6) {
            http_response_code(400);
            convertirJSON(["error" => "La contraseña debe tener al menos 6 caracteres"]);
            return;
        }

        if (!isset($json["rol_id"]) || !is_numeric($json["rol_id"])) {
            http_response_code(400);
            convertirJSON(["error" => "El campo rol_id es obligatorio y debe ser numérico"]);
            return;
        }

        $existente = $this->dao->buscarPorEmail($json["email"]);
        if ($existente) {
            http_response_code(409);
            convertirJSON(["error" => "Ya existe un usuario con ese correo"]);
            return;
        }

        $usuario = new Usuario();
        $usuario->setNombre(trim($json["nombre"]));
        $usuario->setEmail($json["email"]);
        $usuario->setPassword(password_hash($json["password"], PASSWORD_BCRYPT));
        $usuario->setRolId($json["rol_id"]);

        $resultado = $this->dao->createUsuario($usuario);

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function updateUsuario($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getUsuario($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Usuario no encontrado"]);
            return;
        }

        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["nombre"]) || trim($json["nombre"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "El campo nombre es obligatorio"]);
            return;
        }

        if (!isset($json["email"]) || !filter_var($json["email"], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            convertirJSON(["error" => "El correo no es válido"]);
            return;
        }

        if (!isset($json["rol_id"]) || !is_numeric($json["rol_id"])) {
            http_response_code(400);
            convertirJSON(["error" => "El campo rol_id es obligatorio y debe ser numérico"]);
            return;
        }

        $usuario = new Usuario();
        $usuario->setId($id);
        $usuario->setNombre(trim($json["nombre"]));
        $usuario->setEmail($json["email"]);
        $usuario->setRolId($json["rol_id"]);

        $resultado = $this->dao->updateUsuario($usuario);

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }

    public function deleteUsuario($id)
    {
        if (!is_numeric($id)) {
            http_response_code(400);
            convertirJSON(["error" => "ID inválido"]);
            return;
        }

        $existente = $this->dao->getUsuario($id);
        if (!$existente) {
            http_response_code(404);
            convertirJSON(["error" => "Usuario no encontrado"]);
            return;
        }

        $resultado = $this->dao->deleteUsuario($id);

        if (!$resultado) {
            http_response_code(409);
            convertirJSON(["error" => "No se puede eliminar, el usuario está en uso por algún ticket"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => $resultado
        ]);
    }
}