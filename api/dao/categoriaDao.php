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
        // Crea conexion PDO para categorias.
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    public function listaCategorias()
    {
        // Lista catalogo completo ordenado por id.
        $sql = "SELECT * FROM categorias ORDER BY id";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoria($id)
    {
        // Busca una categoria especifica.
        $sql = "SELECT * FROM categorias WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategoria(Categoria $categoria)
    {
        try {
            // Inserta nombre unico de categoria.
            $sql = "INSERT INTO categorias (nombre) VALUES (?)";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$categoria->getNombre()]);
        } catch (PDOException $e) {
            // Puede fallar por nombre duplicado.
            return false;
        }
    }

    public function updateCategoria(Categoria $categoria)
    {
        try {
            // Actualiza el nombre de la categoria.
            $sql = "UPDATE categorias SET nombre = ? WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$categoria->getNombre(), $categoria->getId()]);
        } catch (PDOException $e) {
            // Puede fallar por nombre duplicado.
            return false;
        }
    }

    public function deleteCategoria($id)
    {
        try {
            // MySQL bloquea el borrado si hay tickets usando la categoria.
            $sql = "DELETE FROM categorias WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            // El controlador responde 409.
            return false;
        }
    }
}
