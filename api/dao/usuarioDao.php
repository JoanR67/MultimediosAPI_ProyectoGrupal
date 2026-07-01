<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/usuario.php";

/**
 * ============================================================
 * SECTION: DAO de usuarios
 * ============================================================
 *
 * Centraliza todas las consultas SQL de la tabla `usuarios`.
 * Los errores de base de datos no se imprimen aqui; se retorna false
 * para que el controlador responda con JSON y codigo HTTP correcto.
 */
class UsuarioDAO
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    /**
     * Lista usuarios sin exponer el password.
     */
    public function listaUsuarios()
    {
        $sql = "SELECT id, nombre, email, rol_id, creado_en FROM usuarios ORDER BY id";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un usuario por id sin exponer el password.
     */
    public function getUsuario($id)
    {
        $sql = "SELECT id, nombre, email, rol_id, creado_en FROM usuarios WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un usuario por email. Se usa en login y validaciones.
     */
    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$email]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un email usado por otro usuario.
     *
     * Permite actualizar un usuario manteniendo su mismo correo,
     * pero bloquea usar el correo de otra cuenta.
     */
    public function buscarEmailEnOtroUsuario($email, $id)
    {
        $sql = "SELECT id, nombre, email FROM usuarios WHERE email = ? AND id <> ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$email, $id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un usuario.
     */
    public function createUsuario(Usuario $usuario)
    {
        try {
            $sql = "INSERT INTO usuarios (nombre, email, password, rol_id) VALUES (?, ?, ?, ?)";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([
                $usuario->getNombre(),
                $usuario->getEmail(),
                $usuario->getPassword(),
                $usuario->getRolId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza nombre, email y rol de un usuario.
     */
    public function updateUsuario(Usuario $usuario)
    {
        try {
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol_id = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([
                $usuario->getNombre(),
                $usuario->getEmail(),
                $usuario->getRolId(),
                $usuario->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina un usuario por id.
     */
    public function deleteUsuario($id)
    {
        try {
            $sql = "DELETE FROM usuarios WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
