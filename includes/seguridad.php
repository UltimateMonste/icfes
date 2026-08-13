<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/funciones.php";


/*
|--------------------------------------------------------------------------
| Exigir inicio de sesión
|--------------------------------------------------------------------------
*/

function exigirLogin()
{
    if (!isset($_SESSION['id_usuario'])) {
        redireccionar("../login.php");
    }
}


/*
|--------------------------------------------------------------------------
| Exigir administrador
|--------------------------------------------------------------------------
*/

function exigirAdmin()
{
    exigirLogin();

    if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
        http_response_code(403);
        die("Acceso no autorizado.");
    }
}


/*
|--------------------------------------------------------------------------
| Exigir estudiante
|--------------------------------------------------------------------------
*/

function exigirEstudiante()
{
    exigirLogin();

    /*
    | Verificar que sea estudiante
    */

    if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
        http_response_code(403);
        die("Acceso no autorizado.");
    }


    /*
    | Verificar si debe cambiar contraseña
    */

    if (
        isset($_SESSION['primer_ingreso']) &&
        $_SESSION['primer_ingreso'] == 1
    ) {
        header("Location: ../cambiar_password.php");
        exit;
    }
}