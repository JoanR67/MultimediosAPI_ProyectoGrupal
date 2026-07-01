<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/usuarioDao.php";

/**
 * ============================================================
 * SECTION: Controlador de autenticacion
 * ============================================================
 *
 * Maneja el inicio de sesion de usuarios registrados.
 */
class AutenticacionController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UsuarioDAO();
    }

    /**
     * Valida credenciales y devuelve datos basicos del usuario.
     */
    public function login()
    {
        $json = leerJsonBody();
        $errores = $this->validarLogin($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $email = strtolower(trim($json["email"]));
        $usuario = $this->dao->buscarPorEmail($email);

        if (!$usuario) {
            responderError(404, "Usuario no existe");
            return;
        }

        if (!password_verify($json["password"], $usuario["password"])) {
            responderError(401, "Credenciales incorrectas");
            return;
        }

        responderJSON([
            "success" => true,
            "mensaje" => "Login correcto",
            "usuario" => [
                "id" => $usuario["id"],
                "nombre" => $usuario["nombre"],
                "email" => $usuario["email"],
                "rol_id" => $usuario["rol_id"]
            ]
        ]);
    }

    /**
     * ============================================================
     * SECTION: Validaciones
     * ============================================================
     */
    private function validarLogin($json)
    {
        $errores = [];

        if (!is_array($json)) {
            return ["El body debe ser JSON valido"];
        }

        if (!isset($json["email"]) || !filter_var($json["email"], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El campo email debe ser un correo valido";
        }

        if (!campoTextoValido($json, "password")) {
            $errores[] = "El campo password es obligatorio";
        }

        return $errores;
    }
}
