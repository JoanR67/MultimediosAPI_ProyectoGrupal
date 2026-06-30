<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/usuario.php";

class UsuarioDAO
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaUsuarios()
    {
        $sql = "SELECT id, nombre, email, rol_id, creado_en FROM usuarios";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();
        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsuario($id)
    {
        $sql = "SELECT id, nombre, email, rol_id, creado_en FROM usuarios WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);
        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$email]);
        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createUsuario(Usuario $usuario)
    {
        try {
            $query = "INSERT INTO usuarios (nombre, email, password, rol_id) VALUES (?, ?, ?, ?)";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([
                $usuario->getNombre(),
                $usuario->getEmail(),
                $usuario->getPassword(),
                $usuario->getRolId()
            ]);
        } catch (PDOException $e) {
            echo "Error al crear usuario: " . $e->getMessage();
            return false;
        }
    }

    public function updateUsuario(Usuario $usuario)
    {
        try {
            $query = "UPDATE usuarios SET nombre = ?, email = ?, rol_id = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([
                $usuario->getNombre(),
                $usuario->getEmail(),
                $usuario->getRolId(),
                $usuario->getId()
            ]);
        } catch (PDOException $e) {
            echo "Error al actualizar usuario: " . $e->getMessage();
            return false;
        }
    }

    public function deleteUsuario($id)
    {
        try {
            $query = "DELETE FROM usuarios WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            echo "Error al eliminar usuario: " . $e->getMessage();
            return false;
        }
    }
}