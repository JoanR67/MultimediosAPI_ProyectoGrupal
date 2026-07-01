<?php

/**
 * ============================================================
 * SECTION: Respuestas HTTP y validaciones comunes
 * ============================================================
 *
 * Este archivo concentra funciones simples reutilizadas por los
 * controladores. Su objetivo es mantener respuestas JSON limpias,
 * estados HTTP correctos y validaciones basicas consistentes.
 */

/**
 * Convierte cualquier arreglo u objeto en una respuesta JSON legible.
 *
 * @param mixed $objeto Informacion que se enviara al cliente.
 */
function convertirJSON($objeto)
{
    // Refuerza el header por si el archivo fue llamado directamente desde una ruta.
    header("Content-Type: application/json");

    // JSON_PRETTY_PRINT mejora lectura en Postman; UNESCAPED_UNICODE evita texto escapado.
    echo json_encode(
        $objeto,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
}

/**
 * Devuelve una respuesta JSON con codigo HTTP explicito.
 *
 * @param mixed $objeto Cuerpo de la respuesta.
 * @param int $codigo Codigo HTTP.
 */
function responderJSON($objeto, $codigo = 200)
{
    // Define el codigo HTTP antes de imprimir la respuesta.
    http_response_code($codigo);
    convertirJSON($objeto);
}

/**
 * Devuelve una respuesta de exito con formato uniforme.
 *
 * @param string $mensaje Mensaje visible para quien consume la API.
 * @param array $datos Datos adicionales, por ejemplo un id creado.
 * @param int $codigo Codigo HTTP de exito.
 */
function responderExito($mensaje, $datos = [], $codigo = 200)
{
    // Formato unico para respuestas exitosas de la API.
    $respuesta = [
        "success" => true,
        "mensaje" => $mensaje
    ];

    foreach ($datos as $clave => $valor) {
        // Agrega datos opcionales como id creado sin duplicar formato.
        $respuesta[$clave] = $valor;
    }

    responderJSON($respuesta, $codigo);
}

/**
 * Devuelve una respuesta de error con formato uniforme.
 *
 * @param int $codigo Codigo HTTP de error.
 * @param string $mensaje Mensaje principal del error.
 * @param array|null $detalles Lista opcional de detalles de validacion.
 */
function responderError($codigo, $mensaje, $detalles = null)
{
    // Formato unico para errores controlados de validacion o negocio.
    $respuesta = [
        "success" => false,
        "error" => $mensaje
    ];

    if ($detalles !== null) {
        // Detalles se usa cuando hay varias validaciones fallidas.
        $respuesta["detalles"] = $detalles;
    }

    responderJSON($respuesta, $codigo);
}

/**
 * Lee el body JSON de una peticion POST o PUT.
 *
 * @return array|null Arreglo asociativo o null si el JSON no es valido.
 */
function leerJsonBody()
{
    // Lee el cuerpo raw de la peticion y lo convierte a arreglo asociativo.
    $json = json_decode(file_get_contents("php://input"), true);

    // Devuelve null cuando el body no es JSON valido.
    return is_array($json) ? $json : null;
}

/**
 * Valida ids recibidos por query string.
 *
 * @param mixed $id Valor recibido por GET.
 * @return bool True si es entero positivo.
 */
function esIdValido($id)
{
    // Solo se aceptan enteros positivos para buscar registros por id.
    return filter_var($id, FILTER_VALIDATE_INT) !== false && (int) $id > 0;
}

/**
 * Valida que un campo exista y tenga texto.
 *
 * @param array|null $json Datos recibidos.
 * @param string $campo Nombre del campo.
 * @return bool True si el campo contiene texto.
 */
function campoTextoValido($json, $campo)
{
    // Valida existencia, tipo texto y que no venga vacio.
    return is_array($json)
        && isset($json[$campo])
        && is_string($json[$campo])
        && trim($json[$campo]) !== "";
}

/**
 * Valida que un campo exista y sea numerico.
 *
 * @param array|null $json Datos recibidos.
 * @param string $campo Nombre del campo.
 * @return bool True si el campo es numerico.
 */
function campoNumericoValido($json, $campo)
{
    // Se usa para ids y niveles recibidos en JSON.
    return is_array($json)
        && isset($json[$campo])
        && is_numeric($json[$campo]);
}
