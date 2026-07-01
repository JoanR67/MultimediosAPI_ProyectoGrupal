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
        // El controlador delega las consultas SQL al DAO.
        $this->dao = new UsuarioDAO();
    }

    /**
     * Lista todos los usuarios.
     */
    public function listaUsuarios()
    {
        // Devuelve lista segura: el DAO no expone passwords.
        responderJSON($this->dao->listaUsuarios());
    }

    /**
     * Obtiene un usuario por id.
     */
    public function getUsuario($id)
    {
        // Evita consultar la BD con ids vacios o invalidos.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Busca el usuario antes de responder para manejar 404.
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
        // Lee y decodifica el JSON enviado por Postman/frontend.
        $json = leerJsonBody();
        // En creacion la contraseña es obligatoria.
        $errores = $this->validarDatosUsuario($json, true);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Normaliza el email para evitar duplicados por mayusculas/minusculas.
        $email = strtolower(trim($json["email"]));

        // Bloquea crear dos cuentas con el mismo correo.
        if ($this->dao->buscarPorEmail($email)) {
            responderError(409, "Ya existe un usuario con ese correo");
            return;
        }

        // Construye el modelo que espera el DAO.
        $usuario = new Usuario();
        $usuario->setNombre(trim($json["nombre"]));
        $usuario->setEmail($email);
        // Guarda password hasheado, nunca en texto plano.
        $usuario->setPassword(password_hash($json["password"], PASSWORD_BCRYPT));
        $usuario->setRolId((int) $json["rol_id"]);

        // Ejecuta el INSERT en la tabla usuarios.
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
        // El id es obligatorio para saber que usuario actualizar.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // Verifica existencia antes de intentar modificar.
        if (!$this->dao->getUsuario($id)) {
            responderError(404, "Usuario no encontrado");
            return;
        }

        // Lee los nuevos datos desde el body.
        $json = leerJsonBody();
        // En actualizacion no se pide password.
        $errores = $this->validarDatosUsuario($json, false);

        if (!empty($errores)) {
            responderError(400, "Datos invalidos", $errores);
            return;
        }

        // Normaliza el email que se quiere guardar.
        $email = strtolower(trim($json["email"]));

        // Permite el mismo email del usuario actual, pero no el email de otro usuario.
        if ($this->dao->buscarEmailEnOtroUsuario($email, $id)) {
            responderError(409, "El correo ya pertenece a otro usuario");
            return;
        }

        // Prepara el modelo con los datos actualizados.
        $usuario = new Usuario();
        $usuario->setId((int) $id);
        $usuario->setNombre(trim($json["nombre"]));
        $usuario->setEmail($email);
        $usuario->setRolId((int) $json["rol_id"]);

        // Ejecuta el UPDATE.
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
        // Valida que el id sea entero positivo.
        if (!esIdValido($id)) {
            responderError(400, "ID invalido");
            return;
        }

        // No se puede eliminar algo que no existe.
        if (!$this->dao->getUsuario($id)) {
            responderError(404, "Usuario no encontrado");
            return;
        }

        // El DAO devolvera false si hay llaves foraneas en uso.
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
        // Acumula todos los errores para devolverlos juntos.
        $errores = [];

        if (!is_array($json)) {
            // Si el body no es JSON valido, no tiene sentido seguir validando campos.
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
            // La contraseña minima evita usuarios con passwords demasiado debiles.
            $errores[] = "El campo password debe tener al menos 6 caracteres";
        }

        return $errores;
    }
}
