<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/categoria.php";

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
        $sql = "SELECT * FROM categorias";
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
            $query = "INSERT INTO categorias (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([$categoria->getNombre()]);
        } catch (PDOException $e) {
            echo "Error al crear categoria: " . $e->getMessage();
            return false;
        }
    }

    public function updateCategoria(Categoria $categoria)
    {
        try {
            $query = "UPDATE categorias SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([
                $categoria->getNombre(),
                $categoria->getId()
            ]);
        } catch (PDOException $e) {
            echo "Error al actualizar categoria: " . $e->getMessage();
            return false;
        }
    }

    public function deleteCategoria($id)
    {
        try {
            $query = "DELETE FROM categorias WHERE id = ?";
            $preparado = $this->conexion->prepare($query);
            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            echo "Error al eliminar categoria: " . $e->getMessage();
            return false;
        }
    }
}