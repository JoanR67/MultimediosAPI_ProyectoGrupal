<?php
require_once __DIR__ . '/../controllers/autenticacionController.php';

/**
 * ============================================================
 * SECTION: Ruta de login
 * ============================================================
 */

// Instancia el controlador de autenticacion.
$controlador = new AutenticacionController();
// Login solo acepta POST con email y password en JSON.
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        // Valida credenciales enviadas en el body.
        $controlador->login();
        break;

    default:
        responderError(405, "Metodo no permitido");
}
