<?php

/**
 * ============================================================
 * SECTION: Respuestas HTTP
 * ============================================================
 *
 * Funcion comun para devolver informacion en formato JSON.
 */

/**
 * Convierte cualquier arreglo u objeto en una respuesta JSON legible.
 *
 * @param mixed $objeto Informacion que se enviara al cliente.
 */
function convertirJSON($objeto)
{
    header("Content-Type: application/json");

    echo json_encode(
        $objeto,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
}
