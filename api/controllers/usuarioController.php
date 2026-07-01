<?php
require_once __DIR__ . "/../views/respuesta.php";
require_once __DIR__ . "/../dao/usuarioDao.php";

/**
 * ============================================================
 * SECTION: Controlador de usuarios
 * ============================================================
 *
 * Maneja CRUD de usuarios y valida los datos antes de enviarlos
 * al DAO. Todas las respuestas salen en JSON uniforme.
 */
class UsuarioController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UsuarioDAO();
    }

    /**
     * Lista todos los usuarios.
     */
    public function listaUsuarios()
    {
        responderJSON($this->dao->listaUsuarios());
    }

    /**
     * Obtiene un usuario por id.
     */
    public function getUsuario($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        $usuario = $this->dao->getUsuario($id);

        if (!$usuario) {
            responderError(404, "Usuario no encontrado");
            return;
        }

        responderJSON($usuario);
    }

    /**
     * Crea un usuario nuevo.
     */
    public function createUsuario()
    {
        $json = leerJsonBody();
        $errores = $this->validarDatosUsuario($json, true);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $email = strtolower(trim($json["email"]));

        if ($this->dao->buscarPorEmail($email)) {
            responderError(409, "Ya existe un usuario con ese correo");
            return;
        }

        $usuario = new Usuario();
        $usuario->setNombre(trim($json["nombre"]));
        $usuario->setEmail($email);
        $usuario->setPassword(password_hash($json["password"], PASSWORD_BCRYPT));
        $usuario->setRolId((int) $json["rol_id"]);

        $resultado = $this->dao->createUsuario($usuario);

        if (!$resultado) {
            responderError(400, "No se pudo crear el usuario. Revise el rol_id enviado.");
            return;
        }

        responderExito("Usuario creado correctamente", [], 201);
    }

    /**
     * Actualiza un usuario existente.
     *
     * El correo puede mantenerse igual para el mismo usuario.
     * Si el correo pertenece a otro usuario, se responde 409.
     */
    public function updateUsuario($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getUsuario($id)) {
            responderError(404, "Usuario no encontrado");
            return;
        }

        $json = leerJsonBody();
        $errores = $this->validarDatosUsuario($json, false);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        $email = strtolower(trim($json["email"]));

        if ($this->dao->buscarEmailEnOtroUsuario($email, $id)) {
            responderError(409, "El correo ya pertenece a otro usuario");
            return;
        }

        $usuario = new Usuario();
        $usuario->setId((int) $id);
        $usuario->setNombre(trim($json["nombre"]));
        $usuario->setEmail($email);
        $usuario->setRolId((int) $json["rol_id"]);

        $resultado = $this->dao->updateUsuario($usuario);

        if (!$resultado) {
            responderError(400, "No se pudo actualizar el usuario. Revise el rol_id enviado.");
            return;
        }

        responderExito("Usuario actualizado correctamente");
    }

    /**
     * Elimina un usuario si no esta relacionado con tickets.
     */
    public function deleteUsuario($id)
    {
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        if (!$this->dao->getUsuario($id)) {
            responderError(404, "Usuario no encontrado");
            return;
        }

        $resultado = $this->dao->deleteUsuario($id);

        if (!$resultado) {
            responderError(409, "No se puede eliminar porque el usuario esta en uso");
            return;
        }

        responderExito("Usuario eliminado correctamente");
    }

    /**
     * ============================================================
     * SECTION: Validaciones
     * ============================================================
     *
     * Se separan las validaciones para que create y update usen
     * las mismas reglas sin duplicar codigo.
     */

    private function validarDatosUsuario($json, $requierePassword)
    {
        $errores = [];

        if (!is_array($json)) {
            return ["El body debe ser JSON valido"];
        }

        if (!campoTextoValido($json, "nombre")) {
            $errores[] = "El campo nombre es obligatorio";
        }

        if (!isset($json["email"]) || !filter_var($json["email"], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El campo email debe ser un correo valido";
        }

        if (!campoNumericoValido($json, "rol_id")) {
            $errores[] = "El campo rol_id es obligatorio y debe ser numerico";
        }

        if ($requierePassword && (!isset($json["password"]) || strlen($json["password"]) < 6)) {
            $errores[] = "El campo password debe tener al menos 6 caracteres";
        }

        return $errores;
    }
}
