<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/categoria.php";

/**
 * ============================================================
 * SECTION: DAO de categorias
 * ============================================================
 *
 * Gestiona las consultas SQL de la tabla `categorias`.
 */
class CategoriaDAO
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaCategorias()
    {
        $sql = "SELECT * FROM categorias ORDER BY id";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoria($id)
    {
        $sql = "SELECT * FROM categorias WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategoria(Categoria $categoria)
    {
        try {
            $sql = "INSERT INTO categorias (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$categoria->getNombre()]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateCategoria(Categoria $categoria)
    {
        try {
            $sql = "UPDATE categorias SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$categoria->getNombre(), $categoria->getId()]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteCategoria($id)
    {
        try {
            $sql = "DELETE FROM categorias WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
