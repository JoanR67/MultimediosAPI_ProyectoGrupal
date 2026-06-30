<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/usuarioDao.php";

class AutenticacionController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UsuarioDAO();
    }

    public function login()
    {
        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["email"]) || !filter_var($json["email"], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            convertirJSON(["error" => "El correo no es válido"]);
            return;
        }

        if (!isset($json["password"]) || trim($json["password"]) === "") {
            http_response_code(400);
            convertirJSON(["error" => "La contraseña es obligatoria"]);
            return;
        }

        $usuario = $this->dao->buscarPorEmail($json["email"]);

        if (!$usuario) {
            http_response_code(404);
            convertirJSON(["error" => "Usuario no existe"]);
            return;
        }

        if (!password_verify($json["password"], $usuario["password"])) {
            http_response_code(401);
            convertirJSON(["error" => "Contraseña incorrecta"]);
            return;
        }

        convertirJSON([
            "code" => "200",
            "success" => true,
            "usuario" => [
                "id" => $usuario["id"],
                "nombre" => $usuario["nombre"],
                "email" => $usuario["email"],
                "rol_id" => $usuario["rol_id"]
            ]
        ]);
    }
}