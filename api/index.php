<?php

/**
 * ============================================================
 * SECTION: Punto de entrada de la API
 * ============================================================
 *
 * Este archivo centraliza el acceso a las rutas del proyecto.
 * El recurso se selecciona con el parametro `recurso`.
 *
 * Ejemplos:
 * - /?recurso=tickets
 * - /?recurso=asignaciones
 * - /?recurso=comentarios
 * - /?recurso=historial
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

/**
 * ============================================================
 * SECTION: Mapa de rutas
 * ============================================================
 *
 * Por compatibilidad, si no se envia `recurso`, se usa `tickets`.
 */
$recurso = isset($_GET['recurso']) ? $_GET['recurso'] : 'tickets';

$rutas = [
    'tickets' => __DIR__ . '/routes/api.php',
    'usuarios' => __DIR__ . '/routes/usuarios.php',
    'roles' => __DIR__ . '/routes/roles.php',
    'categorias' => __DIR__ . '/routes/categorias.php',
    'prioridades' => __DIR__ . '/routes/prioridades.php',
    'estados' => __DIR__ . '/routes/estados.php',
    'login' => __DIR__ . '/routes/login.php',
    'asignaciones' => __DIR__ . '/routes/asignaciones.php',
    'comentarios' => __DIR__ . '/routes/comentarios.php',
    'historial' => __DIR__ . '/routes/historial.php'
];

if (!isset($rutas[$recurso])) {
    http_response_code(404);
    echo json_encode(["error" => "Recurso no encontrado"]);
    exit;
}

require_once $rutas[$recurso];
