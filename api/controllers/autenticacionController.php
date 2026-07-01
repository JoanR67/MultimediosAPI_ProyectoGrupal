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
        // Reusa UsuarioDAO para buscar el usuario por email.
        $this->dao = new UsuarioDAO();
    }

    /**
     * Valida credenciales y devuelve datos basicos del usuario.
     */
    public function login()
    {
        // Lee credenciales desde JSON.
        $json = leerJsonBody();
        // Valida formato antes de consultar usuarios.
        $errores = $this->validarLogin($json);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Normaliza email para comparar de forma consistente.
        $email = strtolower(trim($json["email"]));
        // Busca usuario con password incluido.
        $usuario = $this->dao->buscarPorEmail($email);

        if (!$usuario) {
            responderError(404, "Usuario no existe");
            return;
        }

        // Compara la contraseña enviada con el hash guardado.
        if (!password_verify($json["password"], $usuario["password"])) {
            responderError(401, "Credenciales incorrectas");
            return;
        }

        // No se devuelve password en la respuesta.
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
        // Acumula errores de email y password.
        $errores = [];

        if (!is_array($json)) {
            // Body mal formado.
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
