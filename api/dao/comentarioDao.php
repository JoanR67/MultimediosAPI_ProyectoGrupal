<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../models/comentario.php";

/**
 * ============================================================
 * SECTION: DAO de comentarios
 * ============================================================
 *
 * Centraliza las consultas SQL de la tabla `comentarios`.
 */
class ComentarioDAO
{
    private $conexion;

    public function __construct()
    {
        // Crea conexion PDO para comentarios.
        $db = new Conexion();
        $this->conexion = $db->Conectar();
    }

    /**
     * Lista comentarios. Si se envia ticket_id, filtra por ticket.
     */
    public function listaComentarios($ticket_id = null)
    {
        // JOIN permite devolver titulo del ticket y nombre del usuario.
        $sql = "SELECT c.*, t.titulo AS ticket, u.nombre AS usuario
                FROM comentarios c
                INNER JOIN tickets t ON c.ticket_id = t.id
                INNER JOIN usuarios u ON c.usuario_id = u.id";

        if ($ticket_id != null) {
            // Filtro opcional para comentarios de un ticket especifico.
            $sql .= " WHERE c.ticket_id = ?";
        }

        $sql .= " ORDER BY c.id";

        // Prepara la consulta para ejecutar con o sin filtro.
        $preparado = $this->conexion->prepare($sql);
        $ticket_id != null ? $preparado->execute([$ticket_id]) : $preparado->execute();

        return $preparado->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un comentario por id.
     */
    public function getComentario($id)
    {
        // Consulta un comentario puntual por id.
        $sql = "SELECT * FROM comentarios WHERE id = ?";
        $preparado = $this->conexion->prepare($sql);
        $preparado->execute([$id]);

        return $preparado->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un comentario y devuelve su id.
     */
    public function createComentario(Comentario $comentario)
    {
        try {
            // Inserta comentario asociado a ticket y usuario existentes.
            $sql = "INSERT INTO comentarios (ticket_id, usuario_id, contenido) VALUES (?, ?, ?)";
            $preparado = $this->conexion->prepare($sql);
            $preparado->execute([
                $comentario->getTicketId(),
                $comentario->getUsuarioId(),
                $comentario->getContenido()
            ]);

            // Devuelve el id creado para usarlo en pruebas.
            return (int) $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            // Puede fallar por ticket_id o usuario_id inexistente.
            return false;
        }
    }

    /**
     * Actualiza el contenido de un comentario.
     */
    public function updateComentario(Comentario $comentario)
    {
        try {
            // Actualiza relacion y contenido del comentario.
            $sql = "UPDATE comentarios
                    SET ticket_id = ?, usuario_id = ?, contenido = ?
                    WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([
                $comentario->getTicketId(),
                $comentario->getUsuarioId(),
                $comentario->getContenido(),
                $comentario->getId()
            ]);
        } catch (PDOException $e) {
            // El controlador transforma false en respuesta JSON.
            return false;
        }
    }

    /**
     * Elimina un comentario por id.
     */
    public function deleteComentario($id)
    {
        try {
            // Elimina comentario por id.
            $sql = "DELETE FROM comentarios WHERE id = ?";
            $preparado = $this->conexion->prepare($sql);

            return $preparado->execute([$id]);
        } catch (PDOException $e) {
            // Se retorna false ante errores de BD.
            return false;
        }
    }
}
