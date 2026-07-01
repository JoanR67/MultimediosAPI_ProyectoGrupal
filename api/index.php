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

// Muestra errores durante desarrollo local para detectar fallas rapido.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Todas las rutas de esta API responden JSON.
header('Content-Type: application/json');
// Carga helpers de respuesta antes de resolver cualquier ruta.
require_once __DIR__ . '/views/respuesta.php';

/**
 * ============================================================
 * SECTION: Mapa de rutas
 * ============================================================
 *
 * Por compatibilidad, si no se envia `recurso`, se usa `tickets`.
 */
// Si no se especifica recurso, se mantiene compatibilidad usando tickets.
$recurso = isset($_GET['recurso']) ? $_GET['recurso'] : 'tickets';

// Relaciona cada valor de `recurso` con su archivo de rutas.
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
    // Evita incluir archivos inexistentes cuando el recurso no esta registrado.
    responderError(404, "Recurso no encontrado");
    exit;
}

// Carga la ruta solicitada para que ella decida por metodo HTTP.
require_once $rutas[$recurso];
