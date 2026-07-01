<?php
require_once __DIR__ . '/../controllers/autenticacionController.php';

/**
 * ============================================================
 * SECTION: Ruta de login
 * ============================================================
 */

$controlador = new AutenticacionController();
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        $controlador->login();
        break;

    default:
        responderError(405, "Metodo no permitido");
}
